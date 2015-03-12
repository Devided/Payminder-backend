<?php

namespace Tmv\WhatsApi\Message\Received\Media\Info;

use Tmv\WhatsApi\Message\Node\NodeInterface;

class AudioInfoFactory
{
    /**
     * @param  NodeInterface $node
     * @return AudioInfo
     */
    public function createInfo(NodeInterface $node)
    {
        $media = new AudioInfo();
        $media->setCodec($node->getAttribute('acodec'));
        $media->setBitrate($this->convertIntIfValid($node->getAttribute('abitrate')));
        $media->setSampFreq($this->convertIntIfValid($node->getAttribute('asampfreq')));
        $media->setSampFmt($this->convertIntIfValid($node->getAttribute('asampfmt')));

        return $media;
    }

    /**
     * Convert in integer value if <> NULL
     *
     * @param  string $value
     * @return int
     */
    protected function convertIntIfValid($value)
    {
        if (null === $value) {
            return $value;
        }

        return (int) $value;
    }
}
