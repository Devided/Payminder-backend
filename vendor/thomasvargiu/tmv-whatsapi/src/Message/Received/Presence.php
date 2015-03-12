<?php

namespace Tmv\WhatsApi\Message\Received;

class Presence
{
    const TYPE_AVAILABLE = 'available';
    const TYPE_UNAVAILABLE = 'unavailable';

    /**
     * @var string
     */
    protected $from;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $last;

    /**
     * @param  string $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param  string $last
     * @return $this
     */
    public function setLast($last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * @return string
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
