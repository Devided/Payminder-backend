<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Tmv\WhatsApi\Client;

class ReceiptListener extends AbstractListener
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
        $this->listeners[] = $events->attach('received.node.void', [$this, 'onReceivedNodeVoid']);
        $this->listeners[] = $events->attach('received.node.receipt', [$this, 'onReceivedNodeReceipt']);
    }

    public function onReceivedNodeVoid(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();

        if ($node->getAttribute("class") != "message") {
            return;
        }

        $params =[
            'id' => $node->getAttribute('id'),
            'node' => $node,
        ];
        $client->getEventManager()->trigger('onReceiptServer', $client, $params);
    }

    public function onReceivedNodeReceipt(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();
        $params = [
            'id' => $node->getAttribute('id'),
            'node' => $node,
        ];
        $client->getEventManager()->trigger('onReceiptClient', $client, $params);
    }
}
