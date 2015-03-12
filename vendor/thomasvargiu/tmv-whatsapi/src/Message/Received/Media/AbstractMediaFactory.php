<?php

namespace Tmv\WhatsApi\Message\Received\Media;

abstract class AbstractMediaFactory
{
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
