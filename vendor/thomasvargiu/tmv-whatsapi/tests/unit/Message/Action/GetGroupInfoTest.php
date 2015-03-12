<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class GetGroupInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GetGroupInfo
     */
    protected $object;

    public function setUp()
    {
        $this->object = new GetGroupInfo();
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
        $this->object->setGroupId('393921234567-12345689');
        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $expected = array(
            'name'       => 'iq',
            'attributes' =>
                array(
                    'id'    => 'getgroupinfo-',
                    'type'  => 'get',
                    'xmlns' => 'w:g',
                    'to'    => '393921234567-12345689@g.us',
                ),
            'data'       => NULL,
            'children'   =>
                array(
                    array(
                        'name'       => 'query',
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

        $this->object->setGroupId(['group-id']);
        $this->assertTrue($this->object->isValid());
    }
}
