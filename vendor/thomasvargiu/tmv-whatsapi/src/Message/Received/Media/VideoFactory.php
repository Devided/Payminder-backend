<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;
use Tmv\WhatsApi\Message\Received\Media\Info\AudioInfoFactory;
use Tmv\WhatsApi\Message\Received\Media\Info\VideoInfoFactory;

class VideoFactory extends AbstractMediaFactory implements MediaFactoryInterface
{
    /**
     * @var AudioInfoFactory
     */
    protected $audioInfoFactory;
    /**
     * @var VideoInfoFactory
     */
    protected $videoInfoFactory;

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
     * @param  VideoInfoFactory $videoInfoFactory
     * @return $this
     */
    public function setVideoInfoFactory(VideoInfoFactory $videoInfoFactory)
    {
        $this->videoInfoFactory = $videoInfoFactory;

        return $this;
    }

    /**
     * @return VideoInfoFactory
     */
    public function getVideoInfoFactory()
    {
        if (!$this->videoInfoFactory) {
            $this->videoInfoFactory = new VideoInfoFactory();
        }

        return $this->videoInfoFactory;
    }

    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node)
    {
        $media = new Video();
        $media->setType($node->getAttribute('type'));
        $media->setIp($node->getAttribute('ip'));
        $media->setData($node->getData());
        $media->setUrl($node->getAttribute('url'));
        $media->setFile($node->getAttribute('file'));
        $media->setMimeType($node->getAttribute('mimetype'));
        $media->setFileHash($node->getAttribute('filehash'));
        $media->setSize($this->convertIntIfValid($node->getAttribute('size')));
        $media->setSeconds($this->convertIntIfValid($node->getAttribute('seconds')));
        $media->setEncoding($node->getAttribute('encoding'));
        $media->setDuration($this->convertIntIfValid($node->getAttribute('duration')));
        $media->setVideoInfo($this->getVideoInfoFactory()->createInfo($node));
        $media->setAudioInfo($this->getAudioInfoFactory()->createInfo($node));

        return $media;
    }
}
