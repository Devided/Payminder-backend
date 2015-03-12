<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class MessageTextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageText
     */
    protected $object;

    public function setUp()
    {
        $this->object = new MessageText();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSetId()
    {
        $this->object->setId('id-test');
        $this->assertEquals('id-test', $this->object->getId());
    }

    public function testSetTimestamp()
    {
        $this->object->setTimestamp(123456789);
        $this->assertEquals(123456789, $this->object->getTimestamp());
    }

    public function testCreateNode()
    {
        $this->object->setTo('393921234567');
        $this->object->setBody('test-body');
        $this->object->setFromName('test-name');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'message',
            'attributes' =>
                array(
                    'id'   => NULL,
                    't'    => NULL,
                    'to'   => '393921234567@s.whatsapp.net',
                    'type' => 'text',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'x',
                        'attributes' =>
                            array(
                                'xmlns' => 'jabber:x:event',
                            ),
                        'data'       => NULL,
                        'children'   =>
                            array(
                                array(
                                    'name'       => 'server',
                                    'attributes' =>
                                        array(),
                                    'data'       => NULL,
                                    'children'   =>
                                        array(),
                                ),
                            ),
                    ),
                    array(
                        'name'       => 'notify',
                        'attributes' =>
                            array(
                                'xmlns' => 'urn:xmpp:whatsapp',
                                'name'  => 'test-name',
                            ),
                        'data'       => NULL,
                        'children'   =>
                            array(),
                    ),
                    array(
                        'name'       => 'request',
                        'attributes' =>
                            array(
                                'xmlns' => 'urn:xmpp:receipts',
                            ),
                        'data'       => NULL,
                        'children'   =>
                            array(),
                    ),
                    array(
                        'name'       => 'body',
                        'attributes' =>
                            array(),
                        'data'       => 'test-body',
                        'children'   =>
                            array(),
                    ),
                ),
        );

        $this->assertEquals($expected, $ret->toArray());
    }

    public function testIsValid()
    {
        $this->assertFalse($this->object->isValid());

        $this->object->setTo(['test']);
        $this->assertFalse($this->object->isValid());

        $this->object->setFromName(['test']);
        $this->assertFalse($this->object->isValid());

        $this->object->setBody(['test']);

        $this->assertTrue($this->object->isValid());
    }
}
