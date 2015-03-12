<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Action\Presence;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Tmv\WhatsApi\Client;

class SuccessListener extends AbstractListener
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
        $this->listeners[] = $events->attach('received.node.success', [$this, 'onReceivedNode']);
    }

    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();

        $client->setConnected(true);
        $client->writeChallengeData($node->getData());
        $client->getConnection()->getNodeWriter()->setKey($client->getConnection()->getOutputKey());

        $client->send(new Presence($client->getIdentity()->getNickname()));

        // triggering public event
        $client->getEventManager()->trigger('onConnected', $client, ['node' => $node]);
    }
}
