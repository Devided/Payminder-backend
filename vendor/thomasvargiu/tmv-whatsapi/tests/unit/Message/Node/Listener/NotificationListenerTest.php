<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class NotificationListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new NotificationListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once();
        $this->object->attach($eventManagerMock);
    }

    public function testOnReceivedNode()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $client = m::mock('Tmv\\WhatsApi\\Client');
        $client->shouldReceive('sendNode')->once();

        $nodeMock->shouldReceive('getAttribute')->with('type')->twice()->andReturn('status');
        $nodeMock->shouldReceive('hasAttribute')->with('to')->once()->andReturn(true);
        $nodeMock->shouldReceive('getAttribute')->with('to')->once()->andReturn('test-to');
        $nodeMock->shouldReceive('hasAttribute')->with('participant')->once()->andReturn(true);
        $nodeMock->shouldReceive('getAttribute')->with('participant')->once()->andReturn('test-participant');
        $nodeMock->shouldReceive('getAttribute')->with('from')->once()->andReturn('test-from');
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('test-id');

        $nodeMock->shouldReceive('getName')->once()->andReturn('notification');

        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $eventMock->shouldReceive('getTarget')->andReturn($client);

        $this->object->onReceivedNode($eventMock);
    }

    protected function tearDown()
    {
        m::close();
    }
}
