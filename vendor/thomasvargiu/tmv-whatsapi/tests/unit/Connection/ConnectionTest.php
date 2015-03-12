<?php

namespace Tmv\WhatsApi\Connection;

use Mockery as m;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testSettersAndGetters()
    {
        $adapterMock = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');
        $adapterMock2 = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');

        $nodeReaderMock = m::mock('Tmv\\WhatsApi\\Protocol\\BinTree\\NodeReader');
        $nodeWriterMock = m::mock('Tmv\\WhatsApi\\Protocol\\BinTree\\NodeWriter');
        $keyStreamMock = m::mock('Tmv\\WhatsApi\\Protocol\\KeyStream');

        $object = new Connection($adapterMock);

        $this->assertEquals($adapterMock, $object->getAdapter());

        $object->setAdapter($adapterMock2);
        $this->assertEquals($adapterMock2, $object->getAdapter());

        // Lazy loading
        $this->assertInstanceOf('Tmv\\WhatsApi\\Protocol\\BinTree\\NodeReader', $object->getNodeReader());
        $this->assertInstanceOf('Tmv\\WhatsApi\\Protocol\\BinTree\\NodeWriter', $object->getNodeWriter());

        $object->setNodeReader($nodeReaderMock);
        $this->assertEquals($nodeReaderMock, $object->getNodeReader());

        $object->setNodeWriter($nodeWriterMock);
        $this->assertEquals($nodeWriterMock, $object->getNodeWriter());

        $object->setInputKey($keyStreamMock);
        $this->assertEquals($keyStreamMock, $object->getInputKey());

        $object->setOutputKey($keyStreamMock);
        $this->assertEquals($keyStreamMock, $object->getOutputKey());
    }

    public function testConnectMethod()
    {
        $adapterMock = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');
        $adapterMock->shouldReceive('connect')->once();

        $object = new Connection($adapterMock);

        $ret = $object->connect();
        $this->assertEquals($object, $ret);
    }

    public function testDisconnectMethod()
    {
        $adapterMock = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');
        $adapterMock->shouldReceive('disconnect')->once();

        $object = new Connection($adapterMock);

        $ret = $object->disconnect();
        $this->assertEquals($object, $ret);
    }

    public function testSendDataMethod()
    {
        $data = 'mydata';
        $adapterMock = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');
        $adapterMock->shouldReceive('sendData')->once()->with($data);

        $object = new Connection($adapterMock);

        $ret = $object->sendData($data);
        $this->assertEquals($object, $ret);
    }

    public function testReadDataMethod()
    {
        $data = 'mydata';
        $adapterMock = m::mock('Tmv\\WhatsApi\\Connection\\Adapter\\AdapterInterface');
        $adapterMock->shouldReceive('readData')->once()->andReturn($data);

        $object = new Connection($adapterMock);

        $ret = $object->readData();
        $this->assertEquals($data, $ret);
    }
}
