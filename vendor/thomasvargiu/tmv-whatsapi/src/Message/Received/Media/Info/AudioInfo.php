<?php

namespace Tmv\WhatsApi\Message\Received\Media\Info;

class AudioInfo
{
    /**
     * @var int
     */
    protected $bitrate;
    /**
     * @var int
     */
    protected $sampFreq;
    /**
     * @var int
     */
    protected $sampFmt;
    /**
     * @var string
     */
    protected $codec;

    /**
     * @param  int   $bitrate
     * @return $this
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param  string $codec
     * @return $this
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param  int   $sampFmt
     * @return $this
     */
    public function setSampFmt($sampFmt)
    {
        $this->sampFmt = $sampFmt;

        return $this;
    }

    /**
     * @return int
     */
    public function getSampFmt()
    {
        return $this->sampFmt;
    }

    /**
     * @param  int   $sampFreq
     * @return $this
     */
    public function setSampFreq($sampFreq)
    {
        $this->sampFreq = $sampFreq;

        return $this;
    }

    /**
     * @return int
     */
    public function getSampFreq()
    {
        return $this->sampFreq;
    }
}
