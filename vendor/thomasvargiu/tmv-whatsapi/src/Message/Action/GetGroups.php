<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class GetGroups
 * @package Tmv\WhatsApi\Message\Action
 */
class GetGroups extends AbstractAction implements IdAwareInterface
{
    const TYPE_PARTICIPATING = 'participating';

    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $type = 'participating';

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
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Node
     */
    public function createNode()
    {
        $listNode = new Node();
        $listNode->setName('list');
        $listNode->setAttribute('type', $this->getType());

        $node = new Node();
        $node->setName('iq');
        $node->setAttributes([
            "id" => 'getgroups-',
            "type" => "get",
            "xmlns" => "w:g",
            "to" => "g.us",
        ]);
        $node->addChild($listNode);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getType(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
