<?php

namespace Tmv\WhatsApi\Entity;

class MediaFileTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFilepath()
    {
        $entity = new MediaFile();
        $entity->setFilepath('test');
        $this->assertEquals('test', $entity->getFilepath());
    }
}
