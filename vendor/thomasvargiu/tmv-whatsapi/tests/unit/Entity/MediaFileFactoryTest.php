<?php

namespace Tmv\WhatsApi\Entity;

function filesize()
{
    return 1;
}

function pathinfo()
{
    return 'jpg';
}

function mime_content_type()
{
    return 'video/mp4';
}

use Mockery as m;

class MediaFileFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryWithFilepath()
    {
        $mediaServieMock = m::mock('Tmv\WhatsApi\Service\MediaService');
        $factory = new MediaFileFactory($mediaServieMock);

        $mediaFile = $factory->factory('/tmy/file.jpg');
        $this->assertInstanceOf('Tmv\WhatsApi\Entity\MediaFile', $mediaFile);
        $this->assertEquals(1, $mediaFile->getSize());
        $this->assertEquals('jpg', $mediaFile->getExtension());
        $this->assertEquals('video/mp4', $mediaFile->getMimeType());
        $this->assertEquals(MediaFileInterface::TYPE_VIDEO, $mediaFile->getType());
    }

    public function testFactoryWithUrl()
    {
        $mediaServieMock = m::mock('Tmv\WhatsApi\Service\MediaService');
        $mediaServieMock->shouldReceive('downloadRemoteMediaFile')->once();
        $factory = new MediaFileFactory($mediaServieMock);

        $url = 'http://www.test.com/image.jpg';
        $mediaFile = $factory->factory($url);
        $this->assertInstanceOf('Tmv\WhatsApi\Entity\RemoteMediaFile', $mediaFile);
    }

    protected function tearDown()
    {
        m::close();
    }
}
