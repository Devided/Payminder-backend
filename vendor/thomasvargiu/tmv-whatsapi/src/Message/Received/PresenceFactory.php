<?php

namespace Tmv\WhatsApi\Message\Received;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;

class PresenceFactory
{
    /**
     * @param  NodeInterface $node
     * @return Presence
     */
    public function createPresence(NodeInterface $node)
    {
        $presence = new Presence();
        $presence->setFrom(Identity::parseJID($node->getAttribute('from')));
        $presence->setLast($node->getAttribute('last'));
        switch ($node->getAttribute('type')) {
            case 'unavailable':
                $presence->setType(Presence::TYPE_UNAVAILABLE);
                break;

            default:
                $presence->setType(Presence::TYPE_AVAILABLE);
        }

        return $presence;
    }
}
