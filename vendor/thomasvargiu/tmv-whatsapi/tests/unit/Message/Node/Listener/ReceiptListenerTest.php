<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class ReceiptListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReceiptListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ReceiptListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->twice();
        $this->object->attach($eventManagerMock);
    }

    public function testOnReceivedNodeVoid()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $client = m::mock('Tmv\\WhatsApi\\Client');
        $client->shouldReceive('getEventManager')->once()->andReturn($eventManagerMock);

        $eventManagerMock->shouldReceive('trigger')->once();
        $nodeMock->shouldReceive('getAttribute')->with('class')->once()->andReturn('message');
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('testid');
        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $eventMock->shouldReceive('getTarget')->andReturn($client);

        $this->object->onReceivedNodeVoid($eventMock);
    }

    public function testOnReceivedNodeReceipt()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('testid');
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $client = m::mock('Tmv\\WhatsApi\\Client');
        $client->shouldReceive('getEventManager')->once()->andReturn($eventManagerMock);

        $eventManagerMock->shouldReceive('trigger')->once();
        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $eventMock->shouldReceive('getTarget')->andReturn($client);

        $this->object->onReceivedNodeReceipt($eventMock);
    }

    protected function tearDown()
    {
        m::close();
    }
}
