<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class SyncContactsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SyncContacts
     */
    protected $object;

    public function setUp()
    {
        $this->object = new SyncContacts('mynumber');
    }

    public function testSetters()
    {
        $this->object->setContext('test');
        $this->assertEquals('test', $this->object->getContext());

        $this->object->setMode('test');
        $this->assertEquals('test', $this->object->getMode());

        $this->object->setTo('test');
        $this->assertEquals('test', $this->object->getTo());

        $this->object->setId('test');
        $this->assertEquals('test', $this->object->getId());

        $this->object->setIndex(1);
        $this->assertEquals(1, $this->object->getIndex());

        $this->object->setLast('test');
        $this->assertEquals('test', $this->object->isLast());

        $this->object->setNumbers(['number1']);
        $this->assertEquals(['number1'], $this->object->getNumbers());

        $this->object->addNumber('number2');
        $this->assertEquals(['number1', 'number2'], $this->object->getNumbers());
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateNode()
    {
        $this->object->setNumbers(array(
            '+393921234567',
            '+393921234568',
        ));
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'iq',
            'attributes' =>
                array(
                    'id'    => 'sendsync-',
                    'type'  => 'get',
                    'xmlns' => 'urn:xmpp:whatsapp:sync',
                    'to'    => 'mynumber@s.whatsapp.net',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'sync',
                        'attributes' =>
                            array(
                                'mode'    => 'full',
                                'context' => 'registration',
                                'sid'     => ((time() + 11644477200) * 10000000),
                                'index'   => '0',
                                'last'    => 'true',
                            ),
                        'data'       => NULL,
                        'children'   =>
                            array(
                                array(
                                    'name'       => 'user',
                                    'attributes' =>
                                        array(),
                                    'data'       => '+393921234567',
                                    'children'   =>
                                        array(),
                                ),
                                array(
                                    'name'       => 'user',
                                    'attributes' =>
                                        array(),
                                    'data'       => '+393921234568',
                                    'children'   =>
                                        array(),
                                ),
                            ),
                    ),
                ),
        );

        $this->assertEquals($expected, $ret->toArray());
    }

    public function testIsValid()
    {
        $this->assertTrue($this->object->isValid());
    }
}
