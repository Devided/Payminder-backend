<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class Sync
 * @package Tmv\WhatsApi\Message\Action
 * @todo: use adapters
 */
class SyncContacts extends AbstractAction implements IdAwareInterface
{
    const MODE_FULL = 'full';
    const CONTEXT_REGISTRATION = 'registration';

    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var string[]
     */
    protected $numbers = [];
    /**
     * @var string
     */
    protected $mode = 'full';
    /**
     * @var string
     */
    protected $context = 'registration';
    /**
     * @var int
     */
    protected $index = 0;
    /**
     * @var bool
     */
    protected $last = true;

    /**
     * @param string $to The current phone number
     */
    public function __construct($to)
    {
        $this->to = $to;
    }

    /**
     * @param  string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @param  string[] $numbers
     * @return $this
     */
    public function setNumbers(array $numbers)
    {
        $this->numbers = $numbers;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    /**
     * @param string $number
     */
    public function addNumber($number)
    {
        $this->numbers[] = $number;
    }

    /**
     * @param  string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param  int   $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param  boolean $last
     * @return $this
     */
    public function setLast($last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLast()
    {
        return $this->last;
    }

    /**
     * @param  string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return Node
     */
    public function createNode()
    {
        $syncNode = new Node();
        $syncNode->setName('sync');
        $syncNode->setAttributes([
            "mode" => $this->getMode(),
            "context" => $this->getContext(),
            "sid" => "".((time() + 11644477200) * 10000000),
            "index" => "".$this->getIndex(),
            "last" => $this->isLast() ? "true" : "false",
        ]);

        foreach ($this->getNumbers() as $number) {
            $userNode = new Node();
            $userNode->setName('user')
                ->setData($number);
            $syncNode->addChild($userNode);
        }

        $node = new Node();
        $node->setName('iq');
        $node->setAttributes([
            "id" => 'sendsync-',
            "type" => "get",
            "xmlns" => "urn:xmpp:whatsapp:sync",
            "to" => Identity::createJID($this->getTo()),
        ]);
        $node->addChild($syncNode);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getMode(),
            $this->getContext(),
            $this->getTo(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
