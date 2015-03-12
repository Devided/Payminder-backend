<?php

namespace Tmv\WhatsApi\Message\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Node
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Node();
    }

    public function testGetNameMethod()
    {
        $this->assertNull($this->object->getName());
    }

    public function testSetNameMethod()
    {
        $this->object->setName('foo');
        $this->assertEquals('foo', $this->object->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameMethodException()
    {
        $this->object->setName('foo');
        $this->object->setName('baz');
    }
}
