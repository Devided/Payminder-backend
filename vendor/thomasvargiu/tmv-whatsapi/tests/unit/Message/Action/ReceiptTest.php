<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class ReceiptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Receipt
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Receipt();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateNode()
    {
        $this->object->setId('393921234567@server');
        $this->object->setTo('393921234567');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name' => 'receipt',
            'attributes' =>
                array(
                    'id' => '393921234567@server',
                    'to' => '393921234567',
                ),
            'data' => NULL,
            'children' =>
                array(
                ),
        );

        $this->assertEquals($expected, $ret->toArray());
    }

    public function testIsValid()
    {
        $this->assertFalse($this->object->isValid());

        $this->object->setId(['test']);
        $this->assertFalse($this->object->isValid());
        $this->object->setTo(['test']);
        $this->assertTrue($this->object->isValid());
    }
}
