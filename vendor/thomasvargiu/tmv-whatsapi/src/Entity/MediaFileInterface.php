<?php

namespace Tmv\WhatsApi\Entity;

interface MediaFileInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    /**
     * @return string
     */
    public function getType();
}
