<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Message\Action\ActionInterface;
use Tmv\WhatsApi\Message\Action\IdAwareInterface;
use Tmv\WhatsApi\Message\Action\TimestampAwareInterface;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class InjectIdListener extends AbstractListener
{
    /**
     * @var int
     */
    protected $messageCounter = 1;
    /**
     * @var string
     */
    protected $receivedId;

    /**
     * @return int
     */
    public function getMessageCounter()
    {
        return $this->messageCounter;
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
        $this->listeners[] = $events->attach('action.send.pre', [$this, 'onSendingAction']);
        $this->listeners[] = $events->attach('node.send.pre', [$this, 'onSendingNode']);
        $this->listeners[] = $events->attach('node.send.post', [$this, 'onNodeSent']);
        $this->listeners[] = $events->attach('node.received', [$this, 'onNodeReceived']);
    }

    /**
     * @param EventInterface $e
     */
    public function onSendingAction(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var ActionInterface $action */
        $action = $e->getParam('action');
        if ($this->canInjectId($node)) {
            $this->injectNodeId($node);
            if ($action instanceof IdAwareInterface) {
                $action->setId($node->getAttribute('id'));
            }
        }
        if ($node->hasAttribute('t') && null == $node->getAttribute('t')) {
            $this->injectNodeTimestamp($node);
            if ($node instanceof TimestampAwareInterface) {
                $action->setTimestamp($node->getAttribute('t'));
            }
        }
    }

    /**
     * @param  NodeInterface $node
     * @return $this
     */
    protected function injectNodeId(NodeInterface $node)
    {
        $prefix = $node->getAttribute('id') ?: '';
        $node->setAttribute('id', $prefix.$node->getName().'-'.time().'-'.$this->messageCounter++);

        return $this;
    }

    /**
     * @param  NodeInterface $node
     * @return $this
     */
    protected function injectNodeTimestamp(NodeInterface $node)
    {
        $node->setAttribute('t', time());

        return $this;
    }

    public function onSendingNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        if ($this->canInjectId($node)) {
            $this->injectNodeId($node);
        }
        if ($node->hasAttribute('t') && null == $node->getAttribute('t')) {
            $this->injectNodeTimestamp($node);
        }
        $e->setParam('node', $node);
    }

    public function onNodeSent(EventInterface $e)
    {
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();
        if ($node->hasAttribute('id')) {
            $this->waitForServer($client, $node->getAttribute('id'));
        }
    }

    public function onNodeReceived(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        $this->receivedId = $node->hasAttribute('id') ? $node->getAttribute('id') : null;
    }

    /**
     * @param  NodeInterface $node
     * @return bool
     */
    protected function canInjectId(NodeInterface $node)
    {
        if (!$node->hasAttribute('id')) {
            return false;
        }
        $id = $node->getAttribute('id');

        return null === $id || '-' === substr($id, -1, 1);
    }

    /**
     * @param Client $client
     * @param string $id
     * @param int    $timeout
     */
    protected function waitForServer(Client $client, $id, $timeout = 5)
    {
        $time = time();
        do {
            $client->pollMessages();
        } while ($this->receivedId !== $id && time() - $time < $timeout);
    }

    /**
     * @return string
     */
    public function getReceivedId()
    {
        return $this->receivedId;
    }

    /**
     * @param  string $receivedId
     * @return $this
     */
    public function setReceivedId($receivedId)
    {
        $this->receivedId = $receivedId;

        return $this;
    }
}
