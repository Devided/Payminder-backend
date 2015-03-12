<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class Receipt
 *
 * @package Tmv\WhatsApi\Message\Action
 */
class Receipt extends AbstractAction implements IdAwareInterface
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $to;

    /**
     * @param string $to
     * @param string $id
     */
    public function __construct($to = null, $id = null)
    {
        $this->setTo($to);
        $this->setId($id);
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
     * @return Node
     */
    public function createNode()
    {
        $node = new Node();
        $node->setName('receipt')
            ->setAttribute('id', $this->getId())
            ->setAttribute('to', $this->getTo());

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getId(),
            $this->getTo(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
