<?php

namespace Tmv\WhatsApi\Message\Received\Media;

abstract class AbstractMedia implements MediaInterface
{
    /**
     * @var string
     */
    protected $ip;
    /**
     * @var string
     */
    protected $data;
    /**
     * @var string
     */
    protected $type;

    /**
     * @param  string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param  string $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
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
    public function getType()
    {
        return $this->type;
    }
}
