<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Node\Node;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use RuntimeException;
use Tmv\WhatsApi\Client;

class NotificationListener extends AbstractListener
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
        $this->listeners[] = $events->attach('received.node.notification', [$this, 'onReceivedNode']);
    }

    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();

        // @todo: handle notifications public events

        $type = $node->getAttribute("type");
        switch ($type) {
            case "status":
                break;

            case "picture":
                break;

            case "contacts":
                break;

            case "participant":
                break;

            case "subject":
                break;

            default:
                throw new RuntimeException(sprintf("Notification '%s' not implemented", $type));
        }

        $this->sendNotificationAck($client, $node);
    }

    /**
     * @param Client        $client
     * @param NodeInterface $node
     */
    protected function sendNotificationAck(Client $client, NodeInterface $node)
    {
        $ackNode = new Node();

        if ($node->hasAttribute("to")) {
            $ackNode->setAttribute('from', $node->getAttribute("to"));
        }
        if ($node->hasAttribute("participant")) {
            $ackNode->setAttribute('participant', $node->getAttribute("participant"));
        }

        $ackNode->setAttribute('to', $node->getAttribute("from"));
        $ackNode->setAttribute('class', $node->getName());
        $ackNode->setAttribute('id', $node->getAttribute("id"));
        $ackNode->setAttribute('type', $node->getAttribute("type"));
        $client->sendNode($ackNode);
    }
}
