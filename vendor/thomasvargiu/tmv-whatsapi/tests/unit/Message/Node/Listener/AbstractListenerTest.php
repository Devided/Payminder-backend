<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Mockery as m;

class AbstractListenerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testIsNodeFromMyNumber()
    {
        $number1 = '+391234567890';
        $number2 = '+391234567890';
        $phone = m::mock('Tmv\WhatsApi\Entity\Phone[]', [$number1]);
        $identity = m::mock('Tmv\WhatsApi\Entity\Identity[]');
        $identity->setPhone($phone);
        $node = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');

        $node->shouldReceive('getAttribute')->once()->with('from')->andReturn($number2);

        /** @var \Tmv\WhatsApi\Message\Node\Listener\AbstractListener $object */
        $object = $this->getMockForAbstractClass('Tmv\WhatsApi\Message\Node\Listener\AbstractListener');
        $method = $this->getMethod('Tmv\WhatsApi\Message\Node\Listener\AbstractListener', 'isNodeFromMyNumber');
        $ret = $method->invokeArgs($object, [$identity, $node]);
        $this->assertTrue($ret);
    }

    public function testIsNodeFromMyNumberWithDifferentNumber()
    {
        $number1 = '+391234567890';
        $number2 = '+391234567891';
        $phone = m::mock('Tmv\WhatsApi\Entity\Phone[]', [$number1]);
        $identity = m::mock('Tmv\WhatsApi\Entity\Identity[]');
        $identity->setPhone($phone);
        $node = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');

        $node->shouldReceive('getAttribute')->once()->with('from')->andReturn($number2);

        /** @var \Tmv\WhatsApi\Message\Node\Listener\AbstractListener $object */
        $object = $this->getMockForAbstractClass('Tmv\WhatsApi\Message\Node\Listener\AbstractListener');
        $method = $this->getMethod('Tmv\WhatsApi\Message\Node\Listener\AbstractListener', 'isNodeFromMyNumber');
        $ret = $method->invokeArgs($object, [$identity, $node]);
        $this->assertFalse($ret);
    }

    public function testIsNodeFromGroup()
    {
        $from = '1234-1244';
        $node = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');

        $node->shouldReceive('getAttribute')->once()->with('from')->andReturn($from);

        /** @var \Tmv\WhatsApi\Message\Node\Listener\AbstractListener $object */
        $object = $this->getMockForAbstractClass('Tmv\WhatsApi\Message\Node\Listener\AbstractListener');
        $method = $this->getMethod('Tmv\WhatsApi\Message\Node\Listener\AbstractListener', 'isNodeFromGroup');
        $ret = $method->invokeArgs($object, [$node]);
        $this->assertTrue($ret);
    }

    public function testIsNodeNotFromGroup()
    {
        $from = '12341244';
        $node = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');

        $node->shouldReceive('getAttribute')->once()->with('from')->andReturn($from);

        /** @var \Tmv\WhatsApi\Message\Node\Listener\AbstractListener $object */
        $object = $this->getMockForAbstractClass('Tmv\WhatsApi\Message\Node\Listener\AbstractListener');
        $method = $this->getMethod('Tmv\WhatsApi\Message\Node\Listener\AbstractListener', 'isNodeFromGroup');
        $ret = $method->invokeArgs($object, [$node]);
        $this->assertFalse($ret);
    }

    /**
     * @param  string            $class
     * @param  string            $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
