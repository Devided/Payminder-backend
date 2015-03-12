<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

abstract class AbstractListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param  NodeInterface $node
     * @return bool
     */
    protected function isNodeFromMyNumber(Identity $identity, NodeInterface $node)
    {
        $currentPhoneNumber = $identity->getPhone()->getPhoneNumber();

        return 0 === strncmp($node->getAttribute('from'), $currentPhoneNumber, strlen($currentPhoneNumber));
    }

    /**
     * @param  NodeInterface $node
     * @return bool
     */
    protected function isNodeFromGroup(NodeInterface $node)
    {
        return false !== strpos($node->getAttribute('from'), "-");
    }
}
