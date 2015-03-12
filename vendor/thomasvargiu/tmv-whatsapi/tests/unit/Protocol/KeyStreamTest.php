<?php

namespace Tmv\WhatsApi\Protocol;

class KeyStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyStream
     */
    protected $object;

    public function setUp()
    {
        $this->object = new KeyStream(
            hex2bin('c20d51be8b63664561e634f388c636b345f0e6f8'),
            hex2bin('3a20b0809964a791d6f79da949da6ca6fe616771')
        );
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testGenerateKeys($password, $nonce, $expected)
    {
        $ret = $this->object->generateKeys($password, $nonce);
        $this->assertEquals($expected, $ret);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDecodeMessageException()
    {
        $string = bin2hex('mystring');
        $this->object->decodeMessage($string, 8, 0, 8);
    }

    public function testDecodeMessage()
    {
        $ret = $this->object->decodeMessage(
            hex2bin('da0780eb04f87274a234ca1e39c3fda387da3516cb10520ccdf21cd9566eb3b3015370b91ecbfd18bb808595e2500b0c05052d92cb71a459fc947236b66107de246ef8d8602ff83a'),
            68,
            0,
            68
        );
        $expected = hex2bin('f80c9ea0fc0a31343039303234333831503999061ffc0a313338353634363332322efc0a31343137313832333232fc14079d2a299a41f2cde41e9f4aae6db69c68ce88b2602ff83a');
        $this->assertEquals($expected, $ret);
    }

    public function testEncodeMessage()
    {
        $ret = $this->object->encodeMessage('mystring', 8, 0, 8);
        $this->assertEquals(hex2bin('4f726d3f8a9b2d270e987920'), $ret);
    }

    public function passwordProvider()
    {
        $ret = array(
            array(
                'testpassword',
                'testnonce',
                array_map(
                    'hex2bin',
                    array(
                        '9bf1e0a448ee46399718b4a69a34363636f36f71',
                        '4cf7f377147153771234e6dcee591643a1c566b2',
                        '95f2e6732237e696fe47231d860671ad1764c09a',
                        'df48238ab22eb03c395f59d1d391745288cf7105',
                    )
                ),
            ),
        );

        return $ret;
    }
}
