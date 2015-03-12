<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;
use RuntimeException;

class StreamErrorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new StreamErrorListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once();
        $this->object->attach($eventManagerMock);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOnReceivedNode()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');

        $nodeMock->shouldReceive('hasChild')->with('system-shutdown')->once()->andReturn(true);

        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);

        $this->object->onReceivedNode($eventMock);
    }

    protected function tearDown()
    {
        m::close();
    }
}
