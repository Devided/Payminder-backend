<?php

namespace Tmv\WhatsApi\Message\Received;

use Tmv\WhatsApi\Message\Received\Media\MediaInterface;

class MessageMedia extends AbstractMessage
{
    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * @param  MediaInterface $media
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }
}
