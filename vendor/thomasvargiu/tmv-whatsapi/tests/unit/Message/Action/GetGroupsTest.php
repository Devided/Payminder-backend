<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class GetGroupsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GetGroups
     */
    protected $object;

    public function setUp()
    {
        $this->object = new GetGroups();
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
        $this->object->setType('test-type');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'iq',
            'attributes' =>
                array(
                    'id'    => 'getgroups-',
                    'type'  => 'get',
                    'xmlns' => 'w:g',
                    'to'    => 'g.us',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'list',
                        'attributes' =>
                            array(
                                'type' => 'test-type',
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
        $this->assertTrue($this->object->isValid());
    }
}
