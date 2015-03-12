<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class ChastStateListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChatStateListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ChatStateListener();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testAttach()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once()
            ->with('received.node.chatstate', [$this->object, 'onReceivedNode']);
        $this->object->attach($eventManagerMock);
    }

    public function testOnReceivedNodeComposing()
    {
        $from = '+3909876521';
        $phone = m::mock('Tmv\\WhatsApi\\Entity\\Phone[]', ['+39123456789']);
        $identity = m::mock('Tmv\\WhatsApi\\Entity\\Identity[]');
        $identity->setPhone($phone);
        $client = m::mock('Tmv\\WhatsApi\\Client[]', [$identity]);
        $eventManager = m::mock('Zend\\EventManager\\EventManager');
        $client->setEventManager($eventManager);
        $event = m::mock('Zend\\EventManager\\EventInterface');
        /** @var \Tmv\WhatsApi\Message\Node\Node $node */
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\Node[hasChild]');
        $node->setAttribute('from', $from);
        $node->shouldReceive('hasChild')->with('composing')->andReturn(true);
        $node->shouldReceive('hasChild')->with('paused')->andReturn(false);

        $event->shouldReceive('getTarget')->andReturn($client);
        $event->shouldReceive('getParam')->with('node')->andReturn($node);

        $eventManager->shouldReceive('trigger')->once();

        $this->object->onReceivedNode($event);
    }

    public function testOnReceivedNodePaused()
    {
        $from = '+3909876521';
        $phone = m::mock('Tmv\\WhatsApi\\Entity\\Phone[]', ['+39123456789']);
        $identity = m::mock('Tmv\\WhatsApi\\Entity\\Identity[]');
        $identity->setPhone($phone);
        $client = m::mock('Tmv\\WhatsApi\\Client[]', [$identity]);
        $eventManager = m::mock('Zend\\EventManager\\EventManager');
        $client->setEventManager($eventManager);
        $event = m::mock('Zend\\EventManager\\EventInterface');
        /** @var \Tmv\WhatsApi\Message\Node\Node $node */
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\Node[hasChild]');
        $node->setAttribute('from', $from);
        $node->shouldReceive('hasChild')->with('composing')->andReturn(false);
        $node->shouldReceive('hasChild')->with('paused')->andReturn(true);

        $event->shouldReceive('getTarget')->andReturn($client);
        $event->shouldReceive('getParam')->with('node')->andReturn($node);

        $eventManager->shouldReceive('trigger')->once();

        $this->object->onReceivedNode($event);
    }
}
