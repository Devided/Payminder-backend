<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;

class VcardFactory implements MediaFactoryInterface
{
    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node)
    {
        $media = new Vcard();
        $media->setType($node->getAttribute('type'));
        $media->setName($node->getChild('vcard')->getAttribute('name'));
        $media->setData($node->getChild('vcard')->getData());

        return $media;
    }
}
