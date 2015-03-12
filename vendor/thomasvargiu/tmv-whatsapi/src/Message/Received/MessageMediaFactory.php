<?php

namespace Tmv\WhatsApi\Message\Received;

use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use DateTime;
use Tmv\WhatsApi\Message\Received\Media\MediaFactory;

class MessageMediaFactory implements MessageFactoryInterface
{
    /**
     * @var MediaFactory
     */
    protected $mediaFactory;

    /**
     * @param  NodeInterface $node
     * @return MessageMedia
     */
    public function createMessage(NodeInterface $node)
    {
        $message = new MessageMedia();
        $message->setId($node->getAttribute('id'));

        $participant = $node->getAttribute('participant');
        $from = $node->getAttribute('from');

        if ($participant) {
            $message->setFrom(Identity::parseJID($participant));
            $message->setGroupId(Identity::parseJID($from));
        } else {
            $message->setFrom(Identity::parseJID($from));
        }

        $dateTime = new DateTime();
        $dateTime->setTimestamp((int) $node->getAttribute('t'));
        $message->setDateTime($dateTime);
        $message->setNotify($node->getAttribute('notify'));
        $message->setType($node->getAttribute('type'));

        $media = $this->getMediaFactory()->createMedia($node->getChild('media'));
        $message->setMedia($media);

        return $message;
    }

    /**
     * @param  MediaFactory $mediaFactory
     * @return $this
     */
    public function setMediaFactory($mediaFactory)
    {
        $this->mediaFactory = $mediaFactory;

        return $this;
    }

    /**
     * @return MediaFactory
     */
    public function getMediaFactory()
    {
        if (!$this->mediaFactory) {
            $this->mediaFactory = new MediaFactory();
        }

        return $this->mediaFactory;
    }
}
