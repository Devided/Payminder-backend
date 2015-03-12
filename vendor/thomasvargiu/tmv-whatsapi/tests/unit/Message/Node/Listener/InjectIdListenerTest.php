<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class InjectIdListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InjectIdListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new InjectIdListener();
    }

    public function testAttachAndDetachMethod()
    {
        $eventManagerMock = m::mock('Zend\\EventManager\\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->times(4);
        $this->object->attach($eventManagerMock);
    }

    public function testOnSendingNodeNodeMethod()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock(
            'Tmv\\WhatsApi\\Message\\Node\\NodeInterface'
        );
        $client = m::mock('Tmv\\WhatsApi\\Client');

        $nodeMock->shouldReceive('hasAttribute')->with('id')->andReturn(true);
        $nodeMock->shouldReceive('hasAttribute')->with('t')->andReturn(true);
        $nodeMock->shouldReceive('getAttribute')->with('id')->andReturn(null);
        $nodeMock->shouldReceive('getAttribute')->with('t')->andReturn(null);
        $nodeMock->shouldReceive('setAttribute')
            ->with('id', sprintf('%s-%s-%s', 'testname', time(), $this->object->getMessageCounter()))
            ->once();
        $nodeMock->shouldReceive('setAttribute')
            ->with('t', time())
            ->once();
        $nodeMock->shouldReceive('getName')->once()->andReturn('testname');

        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $eventMock->shouldReceive('getTarget')->andReturn($client);
        $eventMock->shouldReceive('setParam')->with('node', $nodeMock);

        $this->object->onSendingNode($eventMock);
    }

    public function testOnNodeSentMethod()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock(
            'Tmv\\WhatsApi\\Message\\Node\\NodeInterface'
        );
        $client = m::mock('Tmv\\WhatsApi\\Client');
        $client->shouldReceive('pollMessages');

        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $eventMock->shouldReceive('getTarget')->andReturn($client);
        $nodeMock->shouldReceive('hasAttribute')->with('id')->andReturn(true);
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('testid');

        // Setting received with to avoid timeout wait
        $this->object->setReceivedId('testid');

        $this->object->onNodeSent($eventMock);
    }

    public function testOnNodeReceivedNodeMethod()
    {
        $eventMock = m::mock('Zend\\EventManager\\EventInterface');
        $nodeMock = m::mock('Tmv\\WhatsApi\\Message\\Node\\NodeInterface');

        $eventMock->shouldReceive('getParam')->with('node')->once()->andReturn($nodeMock);
        $nodeMock->shouldReceive('hasAttribute')->with('id')->once()->andReturn(true);
        $nodeMock->shouldReceive('getAttribute')->with('id')->once()->andReturn('testid');

        $this->object->onNodeReceived($eventMock);
        $this->assertEquals('testid', $this->object->getReceivedId());
    }

    protected function tearDown()
    {
        m::close();
    }
}
