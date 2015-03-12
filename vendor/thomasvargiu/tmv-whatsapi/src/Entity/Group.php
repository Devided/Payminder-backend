<?php

namespace Tmv\WhatsApi\Entity;

use DateTime;

class Group
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $owner;
    /**
     * @var DateTime
     */
    protected $creation;
    /**
     * @var string
     */
    protected $subject;

    /**
     * @param  array $data
     * @return Group
     */
    public static function factory(array $data)
    {
        $group = new self();

        $group->setId($data['id']);
        $group->setOwner(Identity::parseJID($data['owner']));
        $creation = new DateTime();
        $creation->setTimestamp((int) $data['creation']);
        $group->setCreation($creation);
        $group->setSubject($data['subject']);

        return $group;
    }

    /**
     * @param  \DateTime $creation
     * @return $this
     */
    public function setCreation($creation)
    {
        $this->creation = $creation;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreation()
    {
        return $this->creation;
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
     * @param  string $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param  string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
