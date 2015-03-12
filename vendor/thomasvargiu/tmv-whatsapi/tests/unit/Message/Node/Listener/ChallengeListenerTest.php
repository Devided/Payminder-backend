<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use \Mockery as m;

class ChallengeListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChallengeListener
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ChallengeListener();
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
        $node = m::mock('Tmv\\WhatsApi\\Message\\Node\\Challenge');
        $phone = m::mock('Tmv\\WhatsApi\\Entity\\Phone[]', [+39123456789]);
        $identity = m::mock('Tmv\\WhatsApi\\Entity\\Identity[]');
        $client = m::mock('Tmv\\WhatsApi\\Client[sendNode]', [$identity]);

        $identity->setPhone($phone);

        $event->shouldReceive('getParam')->with('node')->once()->andReturn($node);
        $event->shouldReceive('getTarget')->once()->andReturn($client);
        $client->shouldReceive('sendNode')->once()->with(m::type('Tmv\WhatsApi\Message\Node\Node'));
        $node->shouldReceive('getData')->once()->andReturn('123');

        $this->object->onReceivedNode($event);
    }

    protected function tearDown()
    {
        m::close();
    }
}
