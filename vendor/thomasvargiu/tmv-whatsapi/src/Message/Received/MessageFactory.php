<?php

namespace Tmv\WhatsApi\Message\Received;

use InvalidArgumentException;
use Tmv\WhatsApi\Message\Node\NodeInterface;

class MessageFactory implements MessageFactoryInterface
{
    /**
     * @param  NodeInterface             $node
     * @return MessageMedia|MessageText
     * @throws \InvalidArgumentException
     */
    public function createMessage(NodeInterface $node)
    {
        $type = $node->getAttribute('type');
        switch ($type) {
            case AbstractMessage::TYPE_TEXT:
                $factory = new MessageTextFactory();
                break;

            case AbstractMessage::TYPE_MEDIA:
                $factory = new MessageMediaFactory();
                break;

            default:
                throw new InvalidArgumentException("Invalid message type");
        }

        return $factory->createMessage($node);
    }
}
