<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Tmv\WhatsApi\Message\Received\PresenceFactory;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Tmv\WhatsApi\Client;

class PresenceListener extends AbstractListener
{
    /**
     * @var PresenceFactory
     */
    protected $presenceFactory;

    /**
     * @param  PresenceFactory $presenceFactory
     * @return $this
     */
    public function setPresenceFactory($presenceFactory)
    {
        $this->presenceFactory = $presenceFactory;

        return $this;
    }

    /**
     * @return PresenceFactory
     */
    public function getPresenceFactory()
    {
        if (!$this->presenceFactory) {
            $this->presenceFactory = new PresenceFactory();
        }

        return $this->presenceFactory;
    }

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
        $this->listeners[] = $events->attach('received.node.presence', [$this, 'onReceivedNode']);
    }

    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();

        if ($node->getAttribute('status') == 'dirty') {
            // todo: send clear dirty
        }
        if (!$this->isNodeFromMyNumber($client->getIdentity(), $node)) {
            // It's not my message
            if (!$this->isNodeFromGroup($node)) {
                $presence = $this->getPresenceFactory()->createPresence($node);
                $client->getEventManager()
                    ->trigger('onPresenceReceived', $client, ['presence' => $presence, 'node' => $node]);
            } else {
                // Message from group
                $this->parseGroupPresence($client, $node);
            }
        }
    }

    protected function parseGroupPresence(Client $client, NodeInterface $node)
    {
        $groupId = Identity::parseJID($node->getAttribute('from'));
        if (null != $node->getAttribute('add')) {
            $added = Identity::parseJID($node->getAttribute('add'));
            $client->getEventManager()->trigger('onGroupParticipantAdded',
                $client,
                ['group' => $groupId, 'participant' => $added]
            );
        } elseif (null != $node->getAttribute('remove')) {
            $removed = Identity::parseJID($node->getAttribute('remove'));
            $author  = Identity::parseJID($node->getAttribute('author'));
            $client->getEventManager()->trigger('onGroupParticipantRemoved',
                $client,
                [
                    'group' => $groupId,
                    'participant' => $removed,
                    'author' => $author,
                ]
            );
        }
    }
}
