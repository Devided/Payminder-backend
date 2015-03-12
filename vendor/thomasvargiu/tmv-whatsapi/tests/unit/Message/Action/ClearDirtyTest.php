<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class ClearDirtyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClearDirty
     */
    protected $object;

    public function setUp()
    {
        $categories = array('category1', 'category2');
        $this->object = new ClearDirty($categories);
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

    public function testCreateNode()
    {
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'iq',
            'attributes' =>
                array(
                    'type'  => 'set',
                    'to'    => 's.whatsapp.net',
                    'id'    => NULL,
                    'xmlns' => 'urn:xmpp:whatsapp:dirty',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'clean',
                        'attributes' =>
                            array(
                                'type' => 'category1',
                            ),
                        'data'       => NULL,
                        'children'   =>
                            array(),
                    ),
                    array(
                        'name'       => 'clean',
                        'attributes' =>
                            array(
                                'type' => 'category2',
                            ),
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
        $this->object->setCategories([]);
        $this->assertFalse($this->object->isValid());

        $this->object->setCategories(['category']);
        $this->assertTrue($this->object->isValid());
    }
}
