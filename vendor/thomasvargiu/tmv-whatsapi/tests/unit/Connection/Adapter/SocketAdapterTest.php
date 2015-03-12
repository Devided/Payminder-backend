<?php

namespace Tmv\WhatsApi\Connection\Adapter;

use Mockery as m;

function fsockopen($hostname, $port)
{
    return SocketAdapterTest::$functions->fsockopen($hostname, $port);
}

function stream_set_timeout($socket, $sec, $usec)
{
    return SocketAdapterTest::$functions->stream_set_timeout($socket, $sec, $usec);
}

function fclose($socket)
{
    return SocketAdapterTest::$functions->fclose($socket);
}

function fwrite($socket, $data, $length)
{
    return SocketAdapterTest::$functions->fwrite($socket, $data, $length);
}

function fread($socket, $length)
{
    return SocketAdapterTest::$functions->fread($socket, $length);
}

class SocketAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    public static $functions;

    protected function setUp()
    {
        self::$functions = m::mock();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSettersAndGetters()
    {
        $object = new SocketAdapter();

        $this->assertNull($object->getSocket());
        $this->assertNull($object->getHostname());
        $this->assertNull($object->getPort());
        $this->assertEquals(2, $object->getTimeoutSec());
        $this->assertEquals(0, $object->getTimeoutUsec());

        $object->setHostname('my.host.name');
        $this->assertEquals('my.host.name', $object->getHostname());

        $object->setPort(8080);
        $this->assertEquals(8080, $object->getPort());

        $object->setTimeoutSec(999);
        $this->assertEquals(999, $object->getTimeoutSec());

        $object->setTimeoutUsec(999);
        $this->assertEquals(999, $object->getTimeoutUsec());
    }

    public function testConnect()
    {
        $hostname = 'my.host.name';
        $port = 8080;

        $object = new SocketAdapter();
        $object->setHostname($hostname);
        $object->setPort($port);

        $socketMock = 'mysocket';
        self::$functions->shouldReceive('fsockopen')
            ->with($hostname, $port)
            ->once()
            ->andReturn($socketMock);
        self::$functions->shouldReceive('stream_set_timeout')
            ->with($socketMock, $object->getTimeoutSec(), $object->getTimeoutUsec())
            ->once()
            ->andReturn(true);

        $ret = $object->connect();
        $this->assertEquals($socketMock, $object->getSocket());
        $this->assertEquals($object, $ret);
    }

    public function testDisconnect()
    {
        $socketMock = 'mysocket';

        $object = new SocketAdapter();
        $object->setSocket($socketMock);

        self::$functions->shouldReceive('fclose')
            ->with($socketMock)
            ->once()
            ->andReturn($object);

        $ret = $object->disconnect();
        $this->assertNull($object->getSocket());
        $this->assertEquals($object, $ret);
    }

    public function testWriteData()
    {
        $data = 'mydata';

        $socketMock = 'mysocket';

        $object = new SocketAdapter();
        $object->setSocket($socketMock);

        self::$functions->shouldReceive('fwrite')
            ->with($socketMock, $data, strlen($data))
            ->once()
            ->andReturn($object);

        $ret = $object->sendData($data);
        $this->assertNotNull($object->getSocket());
        $this->assertEquals($object, $ret);
    }
}
