<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class ChatState
 *
 * @package Tmv\WhatsApi\Message\Action
 */
class ChatState extends AbstractAction
{
    const STATE_COMPOSING = 'composing';
    const STATE_PAUSED = 'paused';

    /**
     * @var string
     */
    protected $to;
    /**
     * @var string
     */
    protected $state;

    /**
     * @param string $to
     * @param string $state
     */
    public function __construct($to = null, $state = null)
    {
        $this->setTo($to);
        $this->setState($state);
    }

    /**
     * @param  string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param  string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return Node
     */
    public function createNode()
    {
        $state = new Node();
        $state->setName($this->getState());

        $node = new Node();
        $node->setName('chatstate')
            ->setAttribute('to', Identity::createJID($this->getTo()))
            ->addChild($state);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getTo(),
            $this->getState(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
