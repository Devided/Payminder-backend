<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class SuccessListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SuccessListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new SuccessListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once();
        $this->object->attach($eventManagerMock);
    }

    public function testOnReceivedNodeMethod()
    {
        $event = m::mock('Zend\\EventManager\\EventInterface');
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\Success');
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $client = m::mock('Tmv\\WhatsApi\\Client');

        $nodeWriterMock = m::mock('Tmv\\WhatsApi\\Protocol\\BinTree\\NodeWriter');
        $keyStreamMock = m::mock('Tmv\\WhatsApi\\Protocol\\KeyStream');

        $connectionMock = m::mock('Tmv\\WhatsApi\\Connection\\Connection');
        $connectionMock->shouldReceive('getNodeWriter')->andReturn($nodeWriterMock);
        $connectionMock->shouldReceive('getOutputKey')->once()->andReturn($keyStreamMock);

        $event->shouldReceive('getParam')->with('node')->once()->andReturn($node);
        $event->shouldReceive('getTarget')->once()->andReturn($client);

        $node->shouldReceive('getData')->once()->andReturn('the data');

        $nodeWriterMock->shouldReceive('setKey')->once()->with($keyStreamMock);

        $identityMock = m::mock('Tmv\\Entity\\Identity');
        $identityMock->shouldReceive('getNickname')->once()->andReturn('mynickname');

        $client->shouldReceive('getEventManager')->once()->andReturn($eventManagerMock);
        $client->shouldReceive('getConnection')->andReturn($connectionMock);
        $client->shouldReceive('setConnected')->once()->with(true);
        $client->shouldReceive('writeChallengeData')->once()->with('the data');
        $client->shouldReceive('getIdentity')->once()->andReturn($identityMock);
        $client->shouldReceive('send')->once();

        $eventManagerMock->shouldReceive('trigger')
            ->once()
            ->with('onConnected', $client, ['node' => $node]);

        $this->object->onReceivedNode($event);
    }

    protected function tearDown()
    {
        m::close();
    }
}
