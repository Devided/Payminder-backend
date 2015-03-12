<?php

namespace Tmv\WhatsApi\Connection\Adapter;

use Mockery as m;

class SocketAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFactoryDefaults()
    {
        $defaults = array(
            'hostname' => 'c.whatsapp.net',
            'port' => 443,
            'timeout_sec' => 2,
            'timeout_usec' => 0,
        );
        $data = array();
        $ret = SocketAdapterFactory::factory($data);
        $this->assertInstanceOf(__NAMESPACE__.'\\SocketAdapter', $ret);
        $this->assertEquals($defaults['hostname'], $ret->getHostname());
        $this->assertEquals($defaults['port'], $ret->getPort());
        $this->assertEquals($defaults['timeout_sec'], $ret->getTimeoutSec());
        $this->assertEquals($defaults['timeout_usec'], $ret->getTimeoutUsec());
    }

    public function testFactory()
    {
        $data = array(
            'hostname' => 'my-hostname',
            'port' => 8080,
            'timeout_sec' => 5,
            'timeout_usec' => 10,
        );
        $ret = SocketAdapterFactory::factory($data);
        $this->assertInstanceOf(__NAMESPACE__.'\\SocketAdapter', $ret);
        $this->assertEquals($data['hostname'], $ret->getHostname());
        $this->assertEquals($data['port'], $ret->getPort());
        $this->assertEquals($data['timeout_sec'], $ret->getTimeoutSec());
        $this->assertEquals($data['timeout_usec'], $ret->getTimeoutUsec());
    }
}
