<?php

namespace Tmv\WhatsApi\Entity;

class RemoteMediaFileTest extends \PHPUnit_Framework_TestCase
{
    public function testSetUrl()
    {
        $entity = new RemoteMediaFile();
        $entity->setUrl('test');
        $this->assertEquals('test', $entity->getUrl());
    }
}
