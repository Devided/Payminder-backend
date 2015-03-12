<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Action\ClearDirty;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use RuntimeException;
use Tmv\WhatsApi\Client;

class IbListener extends AbstractListener
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
        $this->listeners[] = $events->attach('received.node.ib', [$this, 'onReceivedNode']);
    }

    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();
        foreach ($node->getChildren() as $child) {
            switch ($child->getName()) {
                case "dirty":
                    $action = new ClearDirty([$child->getAttribute("type")]);
                    $client->send($action);
                    break;

                case "offline":

                    break;

                default:
                    throw new RuntimeException("ib handler for ".$child->getName()." not implemented");
            }
        }
    }
}
