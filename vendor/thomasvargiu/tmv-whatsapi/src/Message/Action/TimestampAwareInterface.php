<?php

namespace Tmv\WhatsApi\Message\Action;

interface TimestampAwareInterface
{
    /**
     * @param  int   $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp);

    /**
     * @return string
     */
    public function getTimestamp();
}
