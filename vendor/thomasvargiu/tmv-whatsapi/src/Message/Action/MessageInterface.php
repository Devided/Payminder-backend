<?php

namespace Tmv\WhatsApi\Message\Action;

interface MessageInterface extends ActionInterface, IdAwareInterface, TimestampAwareInterface
{
    /**
     * @return string
     */
    public function getFromName();

    /**
     * @return string
     */
    public function getTo();
}
