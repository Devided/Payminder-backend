<?php

namespace Tmv\WhatsApi\Message\Received;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use DateTime;

class MessageTextFactory implements MessageFactoryInterface
{
    /**
     * @param  NodeInterface $node
     * @return MessageText
     */
    public function createMessage(NodeInterface $node)
    {
        $message = new MessageText();
        $message->setBody($node->getChild('body')->getData());

        $participant = $node->getAttribute('participant');
        $from = $node->getAttribute('from');

        if ($participant) {
            $message->setFrom(Identity::parseJID($participant));
            $message->setGroupId(Identity::parseJID($from));
        } else {
            $message->setFrom(Identity::parseJID($from));
        }

        $message->setId($node->getAttribute('id'));
        $dateTime = new DateTime();
        $dateTime->setTimestamp((int) $node->getAttribute('t'));
        $message->setDateTime($dateTime);
        $message->setNotify($node->getAttribute('notify'));
        $message->setType($node->getAttribute('type'));

        return $message;
    }
}
