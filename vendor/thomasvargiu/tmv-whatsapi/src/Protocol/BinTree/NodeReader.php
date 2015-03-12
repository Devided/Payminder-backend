<?php

namespace Tmv\WhatsApi\Protocol\BinTree;

use Tmv\WhatsApi\Exception\IncompleteMessageException;
use Tmv\WhatsApi\Exception\RuntimeException;
use Tmv\WhatsApi\Message\Node\Node;
use Tmv\WhatsApi\Protocol\KeyStream;
use Tmv\WhatsApi\Protocol\TokenMap;

class NodeReader
{
    /**
     * @var string
     */
    private $input;
    /**
     * @var KeyStream
     */
    private $key;

    /**
     * @return $this
     */
    public function resetKey()
    {
        $this->key = null;

        return $this;
    }

    /**
     * @param  KeyStream $key
     * @return $this
     */
    public function setKey(KeyStream $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param  string|null                                        $input
     * @return \Tmv\WhatsApi\Message\Node\NodeInterface|null
     * @throws \Tmv\WhatsApi\Exception\IncompleteMessageException
     * @throws \Tmv\WhatsApi\Exception\RuntimeException
     */
    public function nextTree($input = null)
    {
        if ($input != null) {
            $this->input = $input;
        }
        $stanzaFlag = ($this->peekInt8() & 0xF0) >> 4;
        $stanzaSize = $this->peekInt16(1);
        if ($stanzaSize > strlen($this->input)) {
            $exception = new IncompleteMessageException("Incomplete message");
            $exception->setInput($this->input);
            throw $exception;
        }
        $this->readInt24();
        if ($stanzaFlag & 8) {
            if (!isset($this->key)) {
                throw new RuntimeException("Encountered encrypted message, missing key");
            }
            $realSize = $stanzaSize - 4;
            $this->input = $this->key->decodeMessage($this->input, $realSize, 0, $realSize);
        }
        if ($stanzaSize > 0) {
            return $this->nextTreeInternal();
        }

        return null;
    }

    /**
     * @param  int                                      $token
     * @return string
     * @throws \Tmv\WhatsApi\Exception\RuntimeException
     */
    protected function getToken($token)
    {
        $subdict = false;
        $ret = TokenMap::getToken($token, $subdict);
        if (!$ret) {
            $token = $this->readInt8();
            $ret = TokenMap::getToken($token, $subdict);
            if (!$ret) {
                throw new RuntimeException("BinTreeNodeReader->getToken: Invalid token $token");
            }
        }

        return $ret;
    }

    /**
     * @param  string                                   $token
     * @return mixed|string
     * @throws \Tmv\WhatsApi\Exception\RuntimeException
     */
    protected function readString($token)
    {
        $ret = "";
        if ($token == -1) {
            throw new RuntimeException("Invalid token $token");
        }
        if (($token > 4) && ($token < 0xf5)) {
            $ret = $this->getToken($token);
        } elseif ($token == 0) {
            $ret = "";
        } elseif ($token == 0xfc) {
            $size = $this->readInt8();
            $ret = $this->fillArray($size);
        } elseif ($token == 0xfd) {
            $size = $this->readInt24();
            $ret = $this->fillArray($size);
        } elseif ($token == 0xfe) {
            $token = $this->readInt8();
            $ret = $this->getToken($token + 0xf5);
        } elseif ($token == 0xfa) {
            $user = $this->readString($this->readInt8());
            $server = $this->readString($this->readInt8());
            if ((strlen($user) > 0) && (strlen($server) > 0)) {
                $ret = $user."@".$server;
            } elseif (strlen($server) > 0) {
                $ret = $server;
            }
        }

        return $ret;
    }

    /**
     * @param  int   $size
     * @return array
     */
    protected function readAttributes($size)
    {
        $attributes = [];
        $attribCount = ($size - 2 + $size % 2) / 2;
        for ($i = 0; $i < $attribCount; $i++) {
            $key = $this->readString($this->readInt8());
            $value = $this->readString($this->readInt8());
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * @return \Tmv\WhatsApi\Message\Node\NodeInterface|null
     */
    protected function nextTreeInternal()
    {
        $token = $this->readInt8();
        $size = $this->readListSize($token);
        $token = $this->readInt8();
        if ($token == 1) {
            $attributes = $this->readAttributes($size);

            return Node::fromArray(
                array(
                    'name'       => 'start',
                    'attributes' => $attributes,
                )
            );
        } elseif ($token == 2) {
            return null;
        }
        $tag = $this->readString($token);
        $attributes = $this->readAttributes($size);
        if (($size % 2) == 1) {
            return Node::fromArray(
                array(
                    'name'       => $tag,
                    'attributes' => $attributes,
                )
            );
        }
        $token = $this->readInt8();
        if ($this->isListTag($token)) {
            $children = $this->readList($token);

            return Node::fromArray(
                array(
                    'name'       => $tag,
                    'attributes' => $attributes,
                    'children'   => is_array($children) ? $children : array(),
                )
            );
        }

        return Node::fromArray(
            array(
                'name'       => $tag,
                'attributes' => $attributes,
                'data'       => $this->readString($token),
            )
        );
    }

    /**
     * @param  string $token
     * @return bool
     */
    protected function isListTag($token)
    {
        return (($token == 248) || ($token == 0) || ($token == 249));
    }

    /**
     * @param  string $token
     * @return array
     */
    protected function readList($token)
    {
        $size = $this->readListSize($token);
        $ret = array();
        for ($i = 0; $i < $size; $i++) {
            array_push($ret, $this->nextTreeInternal());
        }

        return $ret;
    }

    /**
     * @param  string                                   $token
     * @return int
     * @throws \Tmv\WhatsApi\Exception\RuntimeException
     */
    protected function readListSize($token)
    {
        if ($token == 0xf8) {
            $size = $this->readInt8();
        } elseif ($token == 0xf9) {
            $size = $this->readInt16();
        } else {
            throw new RuntimeException("Invalid token $token");
        }

        return $size;
    }

    /**
     * @param  int $offset
     * @return int
     */
    protected function peekInt24($offset = 0)
    {
        $ret = 0;
        if (strlen($this->input) >= (3 + $offset)) {
            $ret = ord(substr($this->input, $offset, 1)) << 16;
            $ret |= ord(substr($this->input, $offset + 1, 1)) << 8;
            $ret |= ord(substr($this->input, $offset + 2, 1)) << 0;
        }

        return $ret;
    }

    /**
     * @return int
     */
    protected function readInt24()
    {
        $ret = $this->peekInt24();
        if (strlen($this->input) >= 3) {
            $this->input = substr($this->input, 3);
        }

        return $ret;
    }

    /**
     * @param  int $offset
     * @return int
     */
    protected function peekInt16($offset = 0)
    {
        $ret = 0;
        if (strlen($this->input) >= (2 + $offset)) {
            $ret = ord(substr($this->input, $offset, 1)) << 8;
            $ret |= ord(substr($this->input, $offset + 1, 1)) << 0;
        }

        return $ret;
    }

    /**
     * @return int
     */
    protected function readInt16()
    {
        $ret = $this->peekInt16();
        if ($ret > 0) {
            $this->input = substr($this->input, 2);
        }

        return $ret;
    }

    /**
     * @param  int $offset
     * @return int
     */
    protected function peekInt8($offset = 0)
    {
        $ret = 0;
        if (strlen($this->input) >= (1 + $offset)) {
            $sbstr = substr($this->input, $offset, 1);
            $ret = ord($sbstr);
        }

        return $ret;
    }

    /**
     * @return int
     */
    protected function readInt8()
    {
        $ret = $this->peekInt8();
        if (strlen($this->input) >= 1) {
            $this->input = substr($this->input, 1);
        }

        return $ret;
    }

    /**
     * @param  int    $len
     * @return string
     */
    protected function fillArray($len)
    {
        $ret = "";
        if (strlen($this->input) >= $len) {
            $ret = substr($this->input, 0, $len);
            $this->input = substr($this->input, $len);
        }

        return $ret;
    }
}
