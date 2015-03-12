<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Received\Media\Info\AudioInfo;

class Audio extends AbstractMediaFile
{
    /**
     * @var int
     */
    protected $seconds;
    /**
     * @var string
     */
    protected $origin;
    /**
     * @var int
     */
    protected $duration;
    /**
     * @var AudioInfo
     */
    protected $audioInfo;

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
     * @param  string $origin
     * @return $this
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
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
}
