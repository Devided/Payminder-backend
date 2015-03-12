<?php

namespace Tmv\WhatsApi\Entity;

class MediaFile extends AbstractMediaFile
{
    /**
     * @var string
     */
    protected $filepath;

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @param  string $filepath
     * @return $this
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }
}
