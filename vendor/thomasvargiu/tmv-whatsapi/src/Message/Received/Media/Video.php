<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Received\Media\Info\AudioInfo;
use Tmv\WhatsApi\Message\Received\Media\Info\VideoInfo;

class Video extends AbstractMediaFile
{
    /**
     * @var int
     */
    protected $seconds;
    /**
     * @var string
     */
    protected $encoding;
    /**
     * @var int
     */
    protected $duration;
    /**
     * @var AudioInfo
     */
    protected $audioInfo;
    /**
     * @var VideoInfo
     */
    protected $videoInfo;

    /**
     * @param  AudioInfo $audioInfo
     * @return $this
     */
    public function setAudioInfo(AudioInfo $audioInfo)
    {
        $this->audioInfo = $audioInfo;

        return $this;
    }

    /**
     * @return AudioInfo
     */
    public function getAudioInfo()
    {
        return $this->audioInfo;
    }

    /**
     * @param  VideoInfo $videoInfo
     * @return $this
     */
    public function setVideoInfo(VideoInfo $videoInfo)
    {
        $this->videoInfo = $videoInfo;

        return $this;
    }

    /**
     * @return VideoInfo
     */
    public function getVideoInfo()
    {
        return $this->videoInfo;
    }

    /**
     * @param  int   $seconds
     * @return $this
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param  int   $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param  string $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
