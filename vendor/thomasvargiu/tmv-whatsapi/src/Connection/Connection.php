<?php

namespace Tmv\WhatsApi\Connection;

use Tmv\WhatsApi\Connection\Adapter\AdapterInterface;
use Tmv\WhatsApi\Protocol\BinTree\NodeReader;
use Tmv\WhatsApi\Protocol\BinTree\NodeWriter;
use Tmv\WhatsApi\Protocol\KeyStream;

class Connection
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var NodeWriter
     */
    protected $nodeWriter;

    /**
     * @var NodeReader
     */
    protected $nodeReader;

    /**
     * @var KeyStream
     */
    protected $inputKey;
    /**
     * @var KeyStream
     */
    protected $outputKey;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param  AdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param  NodeReader $nodeReader
     * @return $this
     */
    public function setNodeReader($nodeReader)
    {
        $this->nodeReader = $nodeReader;

        return $this;
    }

    /**
     * @return NodeReader
     */
    public function getNodeReader()
    {
        if (!$this->nodeReader) {
            $this->nodeReader = new NodeReader();
        }

        return $this->nodeReader;
    }

    /**
     * @param  NodeWriter $nodeWriter
     * @return $this
     */
    public function setNodeWriter($nodeWriter)
    {
        $this->nodeWriter = $nodeWriter;

        return $this;
    }

    /**
     * @return NodeWriter
     */
    public function getNodeWriter()
    {
        if (!$this->nodeWriter) {
            $this->nodeWriter = new NodeWriter();
        }

        return $this->nodeWriter;
    }

    /**
     * @param  KeyStream $inputKey
     * @return $this
     */
    public function setInputKey($inputKey)
    {
        $this->inputKey = $inputKey;

        return $this;
    }

    /**
     * @return KeyStream
     */
    public function getInputKey()
    {
        return $this->inputKey;
    }

    /**
     * @param  KeyStream $outputKey
     * @return $this
     */
    public function setOutputKey($outputKey)
    {
        $this->outputKey = $outputKey;

        return $this;
    }

    /**
     * @return KeyStream
     */
    public function getOutputKey()
    {
        return $this->outputKey;
    }

    /**
     * @return $this
     */
    public function connect()
    {
        $this->getAdapter()->connect();

        return $this;
    }

    /**
     * @return $this
     */
    public function disconnect()
    {
        $this->getAdapter()->disconnect();

        return $this;
    }

    /**
     * @param  string $data
     * @return $this
     */
    public function sendData($data)
    {
        $this->getAdapter()->sendData($data);

        return $this;
    }

    /**
     * @return string
     */
    public function readData()
    {
        return $this->getAdapter()->readData();
    }
}
