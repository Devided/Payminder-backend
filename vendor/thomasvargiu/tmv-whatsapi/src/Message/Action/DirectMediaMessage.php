<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class MessageText
 * Send a text message
 *
 * @package Tmv\WhatsApi\Message\Action
 */
class DirectMediaMessage extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $file;
    /**
     * @var string
     */
    protected $size;
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var string
     */
    protected $iconData;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param  string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param  string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param  string $hash
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getIconData()
    {
        return $this->iconData;
    }

    /**
     * @param  string $icon
     * @return $this
     */
    public function setIconData($icon)
    {
        $this->iconData = $icon;

        return $this;
    }

    /**
     * @return Node
     */
    public function createNode()
    {

        $server = new Node();
        $server->setName('server');

        $x = new Node();
        $x->setName('x')
            ->setAttribute('xmlns', 'jabber:x:event')
            ->addChild($server);

        $notify = new Node();
        $notify->setName('notify')
            ->setAttribute('xmlns', 'urn:xmpp:whatsapp')
            ->setAttribute('name', $this->getFromName());

        $request = new Node();
        $request->setName('request')
            ->setAttribute('xmlns', 'urn:xmpp:receipts');

        $media = new Node();
        $media->setName('media')
            ->setAttribute('xmlns', "urn:xmpp:whatsapp:mms")
            ->setAttribute('type', $this->getType())
            ->setAttribute('url', $this->getUrl())
            ->setAttribute('file', $this->getFile())
            ->setAttribute('size', $this->getSize())
            ->setAttribute('hash', $this->getHash())
            ->setData($this->getIconData() ?: '');

        $node = new Node();
        $node->setName('message')
            ->setAttribute('id', null)
            ->setAttribute('t', null)
            ->setAttribute('to', Identity::createJID($this->getTo()))
            ->setAttribute('type', 'media')
            ->addChild($x)
            ->addChild($notify)
            ->addChild($request)
            ->addChild($media);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getTo(),
            $this->getFromName(),
            $this->getFile(),
            $this->getHash(),
            $this->getSize(),
            $this->getUrl(),
            $this->getType(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
