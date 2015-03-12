<?php

namespace Tmv\WhatsApi\Entity;

use Mockery as m;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Identity
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Identity();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSettersAndGettersMethods()
    {
        $data = array(
            'nickname' => 'my-nickname',
            'password' => 'my-password',
            'token' => 'my-token',
        );

        $phoneMock = m::mock(__NAMESPACE__.'\\Phone');

        $this->object->setNickname($data['nickname']);
        $this->object->setPassword($data['password']);
        $this->object->setToken($data['token']);
        $this->object->setPhone($phoneMock);

        $this->assertEquals($data['nickname'], $this->object->getNickname());
        $this->assertEquals($data['password'], $this->object->getPassword());
        $this->assertEquals($data['token'], $this->object->getToken());
        $this->assertEquals($phoneMock, $this->object->getPhone());
    }

    public function testCreateJID()
    {
        $number = '393921234567@s.whatsapp.net';
        $ret = Identity::createJID($number);
        $this->assertEquals($number, $ret);

        $number = '393921234567';
        $ret = Identity::createJID($number);
        $this->assertEquals($number.'@s.whatsapp.net', $ret);

        // test group
        $number = '393921234567-1425645';
        $ret = Identity::createJID($number);
        $this->assertEquals($number.'@g.us', $ret);
    }

    public function testParseJID()
    {
        $number = '393921234567';
        $ret = Identity::parseJID($number);
        $this->assertEquals($number, $ret);
    }
}
