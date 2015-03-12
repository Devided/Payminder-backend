<?php

namespace Tmv\WhatsApi\Message\Received\Media;

class Vcard extends AbstractMedia implements MediaInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
