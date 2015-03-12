<?php

namespace Tmv\WhatsApi\Entity;

use Tmv\WhatsApi\Service\MediaService;

class MediaFileFactory
{
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * @param MediaService $mediaService
     */
    public function __construct(MediaService $mediaService)
    {
        $this->setMediaService($mediaService);
    }

    /**
     * @return MediaService
     */
    public function getMediaService()
    {
        return $this->mediaService;
    }

    /**
     * @param  MediaService $mediaService
     * @return $this
     */
    public function setMediaService(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;

        return $this;
    }

    /**
     * @param  string                    $location
     * @param  string                    $type
     * @return MediaFile|RemoteMediaFile
     */
    public function factory($location, $type = null)
    {
        if (filter_var($location, FILTER_VALIDATE_URL) !== false) {
            return $this->fromUrl($location, $type);
        }

        return $this->fromFilepath($location, $type);
    }

    /**
     * @param  string    $filepath
     * @param  string    $type
     * @return MediaFile
     */
    public function fromFilepath($filepath, $type = null)
    {
        $mediaFile = $this->createMediaFile();
        $mediaFile->setFilepath($filepath);
        $mediaFile->setSize(filesize($filepath));
        $mediaFile->setExtension(pathinfo($filepath, PATHINFO_EXTENSION));
        $mediaFile->setMimeType(mime_content_type($filepath));
        if (null == $type) {
            $type = $this->checkMediaFileType($mediaFile);
        }
        $mediaFile->setType($type);

        return $mediaFile;
    }

    /**
     * @param  string          $url
     * @param  string          $type
     * @return RemoteMediaFile
     */
    public function fromUrl($url, $type = null)
    {
        $mediaFile = $this->createRemoteMediaFile();
        $mediaFile->setUrl($url);
        $this->getMediaService()->downloadRemoteMediaFile($mediaFile);
        if (null == $type) {
            $type = $this->checkMediaFileType($mediaFile);
        }
        $mediaFile->setType($type);

        return $mediaFile;
    }

    /**
     * @return RemoteMediaFile
     */
    protected function createRemoteMediaFile()
    {
        return new RemoteMediaFile();
    }

    /**
     * @return MediaFile
     */
    protected function createMediaFile()
    {
        return new MediaFile();
    }

    /**
     * @param  MediaFile   $mediaFile
     * @return null|string
     */
    protected function checkMediaFileType(MediaFile $mediaFile)
    {
        $mimeType = $mediaFile->getMimeType();
        if (0 === strpos($mimeType, 'image')) {
            return MediaFileInterface::TYPE_IMAGE;
        } elseif (0 === strpos($mimeType, 'video')) {
            return MediaFileInterface::TYPE_VIDEO;
        } elseif (0 === strpos($mimeType, 'audio')) {
            return MediaFileInterface::TYPE_VIDEO;
        }
        $map = $this->getExtensionTypeMap();
        if (isset($map[$mediaFile->getExtension()])) {
            return $map[$mediaFile->getExtension()];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getExtensionTypeMap()
    {
        return [
            'jpg' => MediaFileInterface::TYPE_IMAGE,
            'jpeg' => MediaFileInterface::TYPE_IMAGE,
            'gif' => MediaFileInterface::TYPE_IMAGE,
            'png' => MediaFileInterface::TYPE_IMAGE,
            'caf' => MediaFileInterface::TYPE_AUDIO,
            'wav' => MediaFileInterface::TYPE_AUDIO,
            'mp3' => MediaFileInterface::TYPE_AUDIO,
            'wma' => MediaFileInterface::TYPE_AUDIO,
            'ogg' => MediaFileInterface::TYPE_AUDIO,
            'aif' => MediaFileInterface::TYPE_AUDIO,
            'aac' => MediaFileInterface::TYPE_AUDIO,
            'm4a' => MediaFileInterface::TYPE_AUDIO,
            '3gp' => MediaFileInterface::TYPE_VIDEO,
            'mp4' => MediaFileInterface::TYPE_VIDEO,
            'mov' => MediaFileInterface::TYPE_VIDEO,
            'avi' => MediaFileInterface::TYPE_VIDEO,
        ];
    }
}
