<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class FailureListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FailureListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new FailureListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once();
        $this->object->attach($eventManagerMock);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testOnReceivedNodeMethod()
    {
        $event = m::mock('Zend\\EventManager\\EventInterface');
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $client = m::mock('Tmv\\WhatsApi\\Client');

        $event->shouldReceive('getParam')->with('node')->once()->andReturn($node);
        $event->shouldReceive('getTarget')->once()->andReturn($client);
        $client->shouldReceive('getEventManager')->once()->andReturn($eventManagerMock);
        $eventManagerMock->shouldReceive('trigger')->once()->with('onLoginFailed', $client, ['node' => $node]);

        $this->object->onReceivedNode($event);
    }

    protected function tearDown()
    {
        m::close();
    }
}
