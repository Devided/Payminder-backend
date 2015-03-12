<?php

namespace Tmv\WhatsApi\Entity;

use DateTime;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Group
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Group();
    }

    public function testSettersAndGettersMethods()
    {
        $this->object->setId('testprop');
        $this->assertEquals('testprop', $this->object->getId());

        $this->object->setOwner('testprop');
        $this->assertEquals('testprop', $this->object->getOwner());

        $this->object->setSubject('testprop');
        $this->assertEquals('testprop', $this->object->getSubject());

        $datetime = new DateTime();
        $this->object->setCreation($datetime);
        $this->assertEquals($datetime, $this->object->getCreation());
    }

    public function testFactory()
    {
        $timestamp = time();
        $data = array(
            'id' => 'testid',
            'owner' => 'testowner',
            'subject' => 'testsubject',
            'creation' => $timestamp,
        );
        $ret = Group::factory($data);
        $this->assertEquals($data['id'], $ret->getId());
        $this->assertEquals($data['owner'], $ret->getOwner());
        $this->assertEquals($data['subject'], $ret->getSubject());
        $datetime = new DateTime();
        $datetime->setTimestamp($data['creation']);
        $this->assertEquals($datetime, $ret->getCreation());
    }
}
