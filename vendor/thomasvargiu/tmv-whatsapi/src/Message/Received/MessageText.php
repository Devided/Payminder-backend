<?php

namespace Tmv\WhatsApi\Message\Received;

class MessageText extends AbstractMessage
{
    /**
     * @var string
     */
    protected $body;

    /**
     * @param  string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
