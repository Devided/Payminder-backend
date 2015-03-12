<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;

class LocationFactory implements MediaFactoryInterface
{
    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node)
    {
        $media = new Location();
        $media->setType($node->getAttribute('type'));
        $media->setEncoding($node->getAttribute('encoding'));
        $media->setName($node->getAttribute('name'));
        $media->setLongitude((float) $node->getAttribute('longitude'));
        $media->setLatitude((float) $node->getAttribute('latitude'));
        $media->setUrl($node->getAttribute('url'));
        $media->setData($node->getData());

        return $media;
    }
}
