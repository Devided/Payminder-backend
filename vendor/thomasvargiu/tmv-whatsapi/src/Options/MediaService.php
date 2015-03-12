<?php

namespace Tmv\WhatsApi\Options;

use Zend\Stdlib\AbstractOptions;

class MediaService extends AbstractOptions
{
    /**
     * @var string
     */
    protected $mediaFolder;
    /**
     * @var int
     */
    protected $fileMaxSize = 1048576;
    /**
     * @var string
     */
    protected $defaultImageIconFilepath;
    /**
     * @var string
     */
    protected $defaultVideoIconFilepath;

    /**
     * @return string
     */
    public function getMediaFolder()
    {
        if (!$this->mediaFolder) {
            $this->mediaFolder = sys_get_temp_dir();
        }

        return $this->mediaFolder;
    }

    /**
     * @param  string $mediaFolder
     * @return $this
     */
    public function setMediaFolder($mediaFolder)
    {
        if (!file_exists($mediaFolder) || !is_writable($mediaFolder)) {
            throw new \InvalidArgumentException("Media folder must exists and writable");
        }
        $this->mediaFolder = $mediaFolder;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileMaxSize()
    {
        return $this->fileMaxSize;
    }

    /**
     * @param  int   $fileMaxSize
     * @return $this
     */
    public function setFileMaxSize($fileMaxSize)
    {
        $this->fileMaxSize = $fileMaxSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultImageIconFilepath()
    {
        if (!$this->defaultImageIconFilepath) {
            $this->defaultImageIconFilepath = __DIR__.'/../../data/ImageIcon.jpg';
        }

        return $this->defaultImageIconFilepath;
    }

    /**
     * @param  string $defaultImageIconPath
     * @return $this
     */
    public function setDefaultImageIconFilepath($defaultImageIconPath)
    {
        if (!file_exists($defaultImageIconPath)) {
            throw new \InvalidArgumentException("Image icon doesn't exist");
        }
        $this->defaultImageIconFilepath = $defaultImageIconPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultVideoIconFilepath()
    {
        if (!$this->defaultVideoIconFilepath) {
            $this->defaultVideoIconFilepath = __DIR__.'/../../data/VideoIcon.jpg';
        }

        return $this->defaultVideoIconFilepath;
    }

    /**
     * @param  string $defaultVideoIconPath
     * @return $this
     */
    public function setDefaultVideoIconFilepath($defaultVideoIconPath)
    {
        if (!file_exists($defaultVideoIconPath)) {
            throw new \InvalidArgumentException("Video icon doesn't exist");
        }
        $this->defaultVideoIconFilepath = $defaultVideoIconPath;

        return $this;
    }
}
