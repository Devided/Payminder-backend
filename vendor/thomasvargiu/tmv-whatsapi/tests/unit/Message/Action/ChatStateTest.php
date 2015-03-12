<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class ChatStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChatState
     */
    protected $object;

    public function setUp()
    {
        $this->object = new ChatState();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateNode()
    {
        $this->object->setTo('393921234567');
        $this->object->setState('test-state');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'chatstate',
            'attributes' =>
                array(
                    'to' => '393921234567@s.whatsapp.net',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'test-state',
                        'attributes' =>
                            array(),
                        'data'       => NULL,
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
        $this->object->setTo('to-id');
        $this->assertFalse($this->object->isValid());

        $this->object->setState(ChatState::STATE_COMPOSING);
        $this->assertTrue($this->object->isValid());
    }
}
