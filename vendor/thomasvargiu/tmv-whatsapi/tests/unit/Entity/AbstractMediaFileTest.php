<?php

namespace Tmv\WhatsApi\Entity;

use Mockery as m;

class AbstractMediaFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tmv\WhatsApi\Entity\AbstractMediaFile
     */
    protected $abstractMediaFile;

    protected function tearDown()
    {
        m::close();
    }

    protected function setUp()
    {
        /** @var \Tmv\WhatsApi\Entity\AbstractMediaFile $class */
        $this->abstractMediaFile = m::mock('Tmv\WhatsApi\Entity\AbstractMediaFile[]');
    }

    public function testSetExtension()
    {
        $this->abstractMediaFile->setExtension('test');
        $this->assertEquals('test', $this->abstractMediaFile->getExtension());
    }

    public function testSetMimeType()
    {
        $this->abstractMediaFile->setMimeType('test');
        $this->assertEquals('test', $this->abstractMediaFile->getMimeType());
    }

    public function testSetSize()
    {
        $this->abstractMediaFile->setSize('test');
        $this->assertEquals('test', $this->abstractMediaFile->getSize());
    }

    public function testSetType()
    {
        $this->abstractMediaFile->setType('test');
        $this->assertEquals('test', $this->abstractMediaFile->getType());
    }
}
