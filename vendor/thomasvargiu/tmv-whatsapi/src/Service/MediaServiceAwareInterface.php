<?php

namespace Tmv\WhatsApi\Service;

interface MediaServiceAwareInterface
{
    /**
     * @param  MediaService $mediaService
     * @return mixed
     */
    public function setMediaService(MediaService $mediaService);

    /**
     * @return MediaService
     */
    public function getMediaService();
}
