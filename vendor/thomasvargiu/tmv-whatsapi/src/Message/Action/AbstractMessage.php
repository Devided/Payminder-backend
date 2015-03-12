<?php

namespace Tmv\WhatsApi\Message\Action;

/**
 * Abstract Class Message
 *
 * @package Tmv\WhatsApi\Message\Action
 */
abstract class AbstractMessage extends AbstractAction implements MessageInterface
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var int
     */
    protected $timestamp;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var string
     */
    protected $fromName = '';

    /**
     * @param string $from
     * @param string $to
     */
    public function __construct($from = null, $to = null)
    {
        $this->setFromName($from);
        $this->setTo($to);
    }

    /**
     * @param  string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param  string $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param  int   $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = (int) $timestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
