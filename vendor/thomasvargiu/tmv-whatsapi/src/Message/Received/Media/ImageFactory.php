<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;

class ImageFactory extends AbstractMediaFactory implements MediaFactoryInterface
{
    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node)
    {
        $media = new Image();
        $media->setType($node->getAttribute('type'));
        $media->setIp($node->getAttribute('ip'));
        $media->setData($node->getData());
        $media->setUrl($node->getAttribute('url'));
        $media->setFile($node->getAttribute('file'));
        $media->setMimeType($node->getAttribute('mimetype'));
        $media->setFileHash($node->getAttribute('filehash'));
        $media->setWidth($this->convertIntIfValid($node->getAttribute('width')));
        $media->setHeight($this->convertIntIfValid($node->getAttribute('height')));
        $media->setSize($this->convertIntIfValid($node->getAttribute('size')));
        $media->setEncoding($node->getAttribute('encoding'));

        return $media;
    }
}
