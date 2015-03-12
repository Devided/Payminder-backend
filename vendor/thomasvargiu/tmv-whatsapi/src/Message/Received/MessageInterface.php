<?php

namespace Tmv\WhatsApi\Message\Received;

use DateTime;

interface MessageInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getFrom();

    /**
     * @return string
     */
    public function getId();

    /**
     * @return DateTime
     */
    public function getDateTime();

    /**
     * @return string
     */
    public function getNotify();

    /**
     * @return string
     */
    public function getGroupId();

    /**
     * @return bool
     */
    public function isFromGroup();
}
