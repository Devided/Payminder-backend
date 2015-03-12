<?php

namespace Tmv\WhatsApi\Message\Action;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\Node;

/**
 * Class MessageText
 * Send a text message
 *
 * @package Tmv\WhatsApi\Message\Action
 */
class MessageText extends AbstractMessage
{
    /**
     * @var string
     */
    protected $body = '';

    /**
     * @param  string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return Node
     */
    public function createNode()
    {
        $server = new Node();
        $server->setName('server');

        $x = new Node();
        $x->setName('x')
            ->setAttribute('xmlns', 'jabber:x:event')
            ->addChild($server);

        $notify = new Node();
        $notify->setName('notify')
            ->setAttribute('xmlns', 'urn:xmpp:whatsapp')
            ->setAttribute('name', $this->getFromName());

        $request = new Node();
        $request->setName('request')
            ->setAttribute('xmlns', 'urn:xmpp:receipts');

        $body = new Node();
        $body->setName('body')
            ->setData($this->getBody());

        $node = new Node();
        $node->setName('message')
            ->setAttribute('id', null)
            ->setAttribute('t', null)
            ->setAttribute('to', Identity::createJID($this->getTo()))
            ->setAttribute('type', 'text')
            ->addChild($x)
            ->addChild($notify)
            ->addChild($request)
            ->addChild($body);

        return $node;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $data = [
            $this->getTo(),
            $this->getFromName(),
            $this->getBody(),
        ];

        return count(array_filter($data)) == count($data);
    }
}
