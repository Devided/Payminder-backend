<?php

namespace Tmv\WhatsApi\Message\Received;

use Mockery as m;
use DateTime;

class MessageMediaFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageMediaFactory
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MessageMediaFactory();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetMediaFactory()
    {
        $ret = $this->object->getMediaFactory();
        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\Media\\MediaFactory', $ret);
    }

    public function testCreateMessage()
    {
        $timestamp = time();
        $mediaNodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');

        $mediaMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\Media\\MediaInterface');

        $mediaFactoryMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\Media\\MediaFactory');
        $mediaFactoryMock->shouldReceive('createMedia')->with($mediaNodeMock)->once()->andReturn($mediaMock);
        $this->object->setMediaFactory($mediaFactoryMock);

        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('participant')->once()->andReturn(null);
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('message-12234567');
        $nodeMock->shouldReceive('getAttribute')->with('t')->once()->andReturn($timestamp);
        $nodeMock->shouldReceive('getAttribute')->with('notify')->once()->andReturn('test-notify');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn('media');
        $nodeMock->shouldReceive('getChild')->with('media')->once()->andReturn($mediaNodeMock);

        $ret = $this->object->createMessage($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\MessageMedia', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertFalse($ret->isFromGroup());
        $this->assertEquals(null, $ret->getGroupId());
        $this->assertEquals('message-12234567', $ret->getId());
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $this->assertEquals($dateTime, $ret->getDateTime());
        $this->assertEquals('test-notify', $ret->getNotify());
        $this->assertEquals('media', $ret->getType());
        $this->assertEquals($mediaMock, $ret->getMedia());
    }

    public function testCreateMessageFromGroup()
    {
        $timestamp = time();
        $mediaNodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');

        $mediaMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\Media\\MediaInterface');

        $mediaFactoryMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\Media\\MediaFactory');
        $mediaFactoryMock->shouldReceive('createMedia')->with($mediaNodeMock)->once()->andReturn($mediaMock);
        $this->object->setMediaFactory($mediaFactoryMock);

        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('393921234567-1234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('participant')->once()->andReturn('393921234567@server');
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('message-12234567');
        $nodeMock->shouldReceive('getAttribute')->with('t')->once()->andReturn($timestamp);
        $nodeMock->shouldReceive('getAttribute')->with('notify')->once()->andReturn('test-notify');
        $nodeMock->shouldReceive('getAttribute')->with('type')->once()->andReturn('media');
        $nodeMock->shouldReceive('getChild')->with('media')->once()->andReturn($mediaNodeMock);

        $ret = $this->object->createMessage($nodeMock);

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Received\\MessageMedia', $ret);
        $this->assertEquals('393921234567', $ret->getFrom());
        $this->assertTrue($ret->isFromGroup());
        $this->assertEquals('393921234567-1234567', $ret->getGroupId());
        $this->assertEquals('message-12234567', $ret->getId());
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        $this->assertEquals($dateTime, $ret->getDateTime());
        $this->assertEquals('test-notify', $ret->getNotify());
        $this->assertEquals('media', $ret->getType());
        $this->assertEquals($mediaMock, $ret->getMedia());
    }
}
