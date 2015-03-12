<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

function hash_file()
{
    return 'test';
}

class RequestFileUploadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestFileUpload
     */
    protected $object;

    public function setUp()
    {
        $this->object = new RequestFileUpload();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSetMediaFile()
    {
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile');
        $this->object->setMediaFile($mediaFile);
        $this->assertSame($mediaFile, $this->object->getMediaFile());
    }

    public function testSetId()
    {
        $this->object->setId('test');
        $this->assertSame('test', $this->object->getId());
    }

    public function testSetTo()
    {
        $this->object->setTo('test');
        $this->assertSame('test', $this->object->getTo());
    }

    public function testCreateNode()
    {
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile');
        $mediaFile->shouldReceive('getType')->andReturn('image');
        $mediaFile->shouldReceive('getSize')->andReturn(1);
        $mediaFile->shouldReceive('getFilepath')->once()->andReturn('image.jpg');

        $this->object->setMediaFile($mediaFile);

        $ret = $this->object->createNode();

        $this->assertInstanceOf('Tmv\\WhatsApi\\Message\\Node\\Node', $ret);

        $hash = base64_encode('test');

        $expected = array(
            'name' => 'iq',
            'attributes' =>
                array(
                    'id' => 'upload-',
                    'to' => 's.whatsapp.net',
                    'type' => 'set',
                    'xmlns' => 'w:m',
                ),
            'data' => null,
            'children' =>
                array(
                    array(
                        'name' => 'media',
                        'attributes' =>
                            array(
                                'hash' => $hash,
                                'type' => 'image',
                                'size' => 1,
                            ),
                        'data' => null,
                        'children' => array(),
                    ),
                ),
        );

        $this->assertEquals($expected, $ret->toArray());
    }

    public function testIsValidFalse()
    {
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile');
        $mediaFile->shouldReceive('getType')->once()->andReturn(null);
        $mediaFile->shouldReceive('getSize')->once()->andReturn(null);
        $mediaFile->shouldReceive('getFilepath')->once()->andReturn(null);
        $this->object->setMediaFile($mediaFile);

        $this->assertFalse($this->object->isValid());
    }

    public function testIsValid()
    {
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile');
        $mediaFile->shouldReceive('getType')->once()->andReturn('image');
        $mediaFile->shouldReceive('getSize')->once()->andReturn(1);
        $mediaFile->shouldReceive('getFilepath')->once()->andReturn('image.jpg');
        $this->object->setMediaFile($mediaFile);

        $this->assertTrue($this->object->isValid());
    }
}
