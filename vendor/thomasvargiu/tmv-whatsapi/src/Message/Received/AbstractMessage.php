<?php

namespace Tmv\WhatsApi\Message\Received;

use DateTime;

abstract class AbstractMessage implements MessageInterface
{
    const TYPE_TEXT = 'text';
    const TYPE_MEDIA = 'media';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $notify;

    /**
     * @var string
     */
    protected $groupId;

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
     * @param  DateTime $dateTime
     * @return $this
     */
    public function setDateTime(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param  string $notify
     * @return $this
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * @param  string $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return bool
     */
    public function isFromGroup()
    {
        return null !== $this->getGroupId();
    }
}
