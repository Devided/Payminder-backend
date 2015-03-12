<?php

namespace Tmv\WhatsApi\Message\Received\Media;

use Tmv\WhatsApi\Message\Node\NodeInterface;
use RuntimeException;

class MediaFactory implements MediaFactoryInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_VCARD = 'vcard';
    const TYPE_LOCATION = 'location';

    /**
     * @param  NodeInterface    $node
     * @return MediaInterface
     * @throws RuntimeException
     */
    public function createMedia(NodeInterface $node)
    {
        $type = $node->getAttribute('type');

        $factory = null;
        switch ($type) {
            case self::TYPE_IMAGE:
                $factory = new ImageFactory();
                break;

            case self::TYPE_VIDEO:
                $factory = new VideoFactory();
                break;

            case self::TYPE_AUDIO:
                $factory = new AudioFactory();
                break;

            case self::TYPE_VCARD:
                $factory = new VcardFactory();
                break;

            case self::TYPE_LOCATION:
                $factory = new LocationFactory();
                break;
        }

        if (!$factory) {
            throw new RuntimeException("Media type unknown");
        }

        return $factory->createMedia($node);
    }
}
