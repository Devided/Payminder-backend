<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Tmv\WhatsApi\Client;

class ChatStateListener extends AbstractListener
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
        $this->listeners[] = $events->attach('received.node.chatstate', [$this, 'onReceivedNode']);
    }

    /**
     * @param EventInterface $e
     */
    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();
        $identity = $client->getIdentity();

        if ($this->isNodeFromMyNumber($identity, $node) || $this->isNodeFromGroup($node)) {
            return;
        }

        if ($node->hasChild('composing')) {
            $client->getEventManager()->trigger('onMessageComposing',
                $client,
                array(
                    'node' => $node,
                    'from' => Identity::parseJID($node->getAttribute('from')),
                    'id' => $node->getAttribute('id'),
                    'timestamp' => (int) $node->getAttribute('t'),
                )
            );
        } elseif ($node->hasChild('paused')) {
            $client->getEventManager()->trigger('onMessagePaused',
                $client,
                array(
                    'node' => $node,
                    'from' => Identity::parseJID($node->getAttribute('from')),
                    'id' => $node->getAttribute('id'),
                    'timestamp' => (int) $node->getAttribute('t'),
                )
            );
        }
    }
}
