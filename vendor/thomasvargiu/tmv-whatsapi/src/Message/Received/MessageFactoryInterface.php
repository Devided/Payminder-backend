<?php

namespace Tmv\WhatsApi\Message\Received;

use Tmv\WhatsApi\Message\Node\NodeInterface;

interface MessageFactoryInterface
{
    /**
     * @param  NodeInterface    $node
     * @return MessageInterface
     */
    public function createMessage(NodeInterface $node);
}
