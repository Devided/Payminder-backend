<?php

namespace Tmv\WhatsApi\Message\Action;

interface IdAwareInterface
{
    /**
     * @param  string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getId();
}
