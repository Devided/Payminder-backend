<?php

namespace Tmv\WhatsApi\Message\Received\Media\Info;

class VideoInfo
{
    /**
     * @var int
     */
    protected $bitrate;
    /**
     * @var string
     */
    protected $codec;
    /**
     * @var int
     */
    protected $fps;
    /**
     * @var int
     */
    protected $width;
    /**
     * @var int
     */
    protected $height;

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
     * @param  int   $fps
     * @return $this
     */
    public function setFps($fps)
    {
        $this->fps = $fps;

        return $this;
    }

    /**
     * @return int
     */
    public function getFps()
    {
        return $this->fps;
    }

    /**
     * @param  int   $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param  int   $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
