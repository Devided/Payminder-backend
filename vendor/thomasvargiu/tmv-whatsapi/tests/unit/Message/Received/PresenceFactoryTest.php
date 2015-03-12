<?php

namespace Tmv\WhatsApi\Message\Received;

use Mockery as m;

class PresenceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PresenceFactory
     */
    protected $object;

    public function setUp()
    {
        $this->object = new PresenceFactory();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreatePresence()
    {
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('last')->once()->andReturn('test-last');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn('unavailable');

        $ret = $this->object->createPresence($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\Presence', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertEquals('test-last', $ret->getLast());
        $this->assertEquals('unavailable', $ret->getType());
    }

    public function testCreatePresenceWithNoType()
    {
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('last')->once()->andReturn('test-last');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn(null);

        $ret = $this->object->createPresence($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\Presence', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertEquals('test-last', $ret->getLast());
        $this->assertEquals('available', $ret->getType());
    }
}
