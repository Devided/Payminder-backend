<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class PresenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Presence
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Presence();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateNode()
    {
        $this->object->setName('test-name');
        $this->object->setLast('test-last');
        $this->object->setType('test-type');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name' => 'presence',
            'attributes' =>
                array(
                    'name' => 'test-name',
                    'type' => 'test-type',
                    'last' => 'test-last',
                ),
            'data' => NULL,
            'children' =>
                array(
                ),
        );

        $this->assertEquals($expected, $ret->toArray());
    }

    public function testIsValid()
    {
        $this->assertFalse($this->object->isValid());

        $this->object->setName(['test']);
        $this->assertTrue($this->object->isValid());
    }
}
