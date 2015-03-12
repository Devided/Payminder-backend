<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class MessageListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MessageListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('\Zend\EventManager\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once();
        $this->object->attach($eventManagerMock);
    }

    public function testOnReceivedNodeMethod()
    {
        $object = $this->object;
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\Node');
        $node->shouldReceive('getAttribute')->with('from')->andReturn('from-value');
        $node->shouldReceive('getAttribute')->with('id')->andReturn('id-value');
        $messageMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\MessageInterface');

        $messageReceivedFactoryMock = m::mock('Tmv\\WhatsApi\\Message\\Received\\MessageFactory');
        $messageReceivedFactoryMock->shouldReceive('createMessage')->with($node)->andReturn($messageMock);

        $this->object->setMessageReceivedFactory($messageReceivedFactoryMock);

        $event = m::mock('Zend\\EventManager\\EventInterface');
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('trigger')->twice();

        $client = m::mock('Tmv\\WhatsApi\\Client');

        $event->shouldReceive('getParam')->with('node')->once()->andReturn($node);
        $event->shouldReceive('getTarget')->andReturn($client);
        $client->shouldReceive('getEventManager')->andReturn($eventManagerMock);
        $client->shouldReceive('send')->once();

        $object->onReceivedNode($event);
    }

    protected function tearDown()
    {
        m::close();
    }
}
