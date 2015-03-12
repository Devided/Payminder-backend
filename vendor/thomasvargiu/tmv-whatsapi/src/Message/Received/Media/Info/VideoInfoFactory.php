<?php

namespace Tmv\WhatsApi\Message\Received\Media\Info;

use Tmv\WhatsApi\Message\Node\NodeInterface;

class VideoInfoFactory
{
    /**
     * @param  NodeInterface $node
     * @return VideoInfo
     */
    public function createInfo(NodeInterface $node)
    {
        $media = new VideoInfo();
        $media->setWidth($this->convertIntIfValid($node->getAttribute('width')));
        $media->setHeight($this->convertIntIfValid($node->getAttribute('height')));
        $media->setCodec($node->getAttribute('vcodec'));
        $media->setFps($this->convertIntIfValid($node->getAttribute('fps')));
        $media->setBitrate($this->convertIntIfValid($node->getAttribute('vbitrate')));

        return $media;
    }

    /**
     * Convert in integer value if <> NULL
     *
     * @param  string $value
     * @return int
     */
    protected function convertIntIfValid($value)
    {
        if (null === $value) {
            return $value;
        }

        return (int) $value;
    }
}
