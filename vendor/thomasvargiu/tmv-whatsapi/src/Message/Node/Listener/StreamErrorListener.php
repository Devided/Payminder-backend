<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use RuntimeException;

class StreamErrorListener extends AbstractListener
{
    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('received.node.stream:error', [$this, 'onReceivedNode']);
    }

    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        if ($node->hasChild("system-shutdown")) {
            throw new RuntimeException('Stream error: system shutdown');
        }
        if ($node->hasChild('text') && $node->getChild('text')->getData() !== '') {
            throw new RuntimeException("Stream error:".$node->getChild('text')->getData());
        }
        throw new RuntimeException("Stream error: an error occurred");
    }
}
