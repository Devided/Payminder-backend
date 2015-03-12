<?php

namespace Tmv\WhatsApi\Message\Received;

use Mockery as m;
use DateTime;

class MessageTextFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageTextFactory
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MessageTextFactory();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateMessage()
    {
        $timestamp = time();
        $bodyNodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $bodyNodeMock->shouldReceive('getData')->once()->andReturn('test-body');

        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('participant')->once()->andReturn(null);
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('message-12234567');
        $nodeMock->shouldReceive('getAttribute')->with('t')->once()->andReturn($timestamp);
        $nodeMock->shouldReceive('getAttribute')->with('notify')->once()->andReturn('test-notify');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn('text');
        $nodeMock->shouldReceive('getChild')->with('body')->once()->andReturn($bodyNodeMock);

        $ret = $this->object->createMessage($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\MessageText', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertFalse($ret->isFromGroup());
        $this->assertEquals(null, $ret->getGroupId());
        $this->assertEquals('message-12234567', $ret->getId());
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $this->assertEquals($dateTime, $ret->getDateTime());
        $this->assertEquals('test-notify', $ret->getNotify());
        $this->assertEquals('text', $ret->getType());
        $this->assertEquals('test-body', $ret->getBody());
    }

    public function testCreateMessageFromGroup()
    {
        $timestamp = time();
        $bodyNodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $bodyNodeMock->shouldReceive('getData')->once()->andReturn('test-body');

        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567-1234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('participant')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('message-12234567');
        $nodeMock->shouldReceive('getAttribute')->with('t')->once()->andReturn($timestamp);
        $nodeMock->shouldReceive('getAttribute')->with('notify')->once()->andReturn('test-notify');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn('text');
        $nodeMock->shouldReceive('getChild')->with('body')->once()->andReturn($bodyNodeMock);

        $ret = $this->object->createMessage($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\MessageText', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertTrue($ret->isFromGroup());
        $this->assertEquals('393921234567-1234567', $ret->getGroupId());
        $this->assertEquals('message-12234567', $ret->getId());
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $this->assertEquals($dateTime, $ret->getDateTime());
        $this->assertEquals('test-notify', $ret->getNotify());
        $this->assertEquals('text', $ret->getType());
        $this->assertEquals('test-body', $ret->getBody());
    }
}
