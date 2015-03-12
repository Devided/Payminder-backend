<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;
use Tmv\WhatsApi\Message\Received\Media\Info\AudioInfoFactory;

class AudioFactory extends AbstractMediaFactory implements MediaFactoryInterface
{
    /**
     * @var AudioInfoFactory
     */
    protected $audioInfoFactory;

    /**
     * @param  AudioInfoFactory $audioInfoFactory
     * @return $this
     */
    public function setAudioInfoFactory(AudioInfoFactory $audioInfoFactory)
    {
        $this->audioInfoFactory = $audioInfoFactory;

        return $this;
    }

    /**
     * @return AudioInfoFactory
     */
    public function getAudioInfoFactory()
    {
        if (!$this->audioInfoFactory) {
            $this->audioInfoFactory = new AudioInfoFactory();
        }

        return $this->audioInfoFactory;
    }

    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node)
    {
        $media = new Audio();
        $media->setType($node->getAttribute('type'));
        $media->setIp($node->getAttribute('ip'));
        $media->setData($node->getData());
        $media->setUrl($node->getAttribute('url'));
        $media->setFile($node->getAttribute('file'));
        $media->setMimeType($node->getAttribute('mimetype'));
        $media->setFileHash($node->getAttribute('filehash'));
        $media->setSize($this->convertIntIfValid($node->getAttribute('size')));
        $media->setSeconds($this->convertIntIfValid($node->getAttribute('seconds')));
        $media->setDuration($this->convertIntIfValid($node->getAttribute('duration')));
        $media->setAudioInfo($this->getAudioInfoFactory()->createInfo($node));

        return $media;
    }
}
