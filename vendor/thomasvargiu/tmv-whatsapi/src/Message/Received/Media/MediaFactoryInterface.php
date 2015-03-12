<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;

interface MediaFactoryInterface
{
    /**
     * @param  NodeInterface  $node
     * @return MediaInterface
     */
    public function createMedia(NodeInterface $node);
}
