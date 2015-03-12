<?php

namespace Tmv\WhatsApi\Message\Action;

use Mockery as m;

class DirectMediaMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectMediaMessage
     */
    protected $action;

    protected function setUp()
    {
        $this->action = new DirectMediaMessage();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateNode()
    {
        $this->action->setTo('test-to')
            ->setType('image')
            ->setFile('test-file')
            ->setHash('test-has')
            ->setSize(1024)
            ->setUrl('test-url')
            ->setFromName('test-nickname')
            ->setId('test-id')
            ->setTimestamp(1)
            ->setIconData('base64');

        $node = $this->action->createNode();

        $expect = array(
            'name' => 'message',
            'attributes' =>
                array(
                    'id' => NULL,
                    't' => NULL,
                    'to' => 'test-to@g.us',
                    'type' => 'media',
                ),
            'data' => NULL,
            'children' =>
                array(
                    0 =>
                        array(
                            'name' => 'x',
                            'attributes' =>
                                array(
                                    'xmlns' => 'jabber:x:event',
                                ),
                            'data' => NULL,
                            'children' =>
                                array(
                                    0 =>
                                        array(
                                            'name' => 'server',
                                            'attributes' =>
                                                array(
                                                ),
                                            'data' => NULL,
                                            'children' =>
                                                array(
                                                ),
                                        ),
                                ),
                        ),
                    1 =>
                        array(
                            'name' => 'notify',
                            'attributes' =>
                                array(
                                    'xmlns' => 'urn:xmpp:whatsapp',
                                    'name' => 'test-nickname',
                                ),
                            'data' => NULL,
                            'children' =>
                                array(
                                ),
                        ),
                    2 =>
                        array(
                            'name' => 'request',
                            'attributes' =>
                                array(
                                    'xmlns' => 'urn:xmpp:receipts',
                                ),
                            'data' => NULL,
                            'children' =>
                                array(
                                ),
                        ),
                    3 =>
                        array(
                            'name' => 'media',
                            'attributes' =>
                                array(
                                    'xmlns' => 'urn:xmpp:whatsapp:mms',
                                    'type' => 'image',
                                    'url' => 'test-url',
                                    'file' => 'test-file',
                                    'size' => 1024,
                                    'hash' => 'test-has',
                                ),
                            'data' => 'base64',
                            'children' =>
                                array(
                                ),
                        ),
                ),
        );

        $this->assertEquals($expect, $node->toArray());
    }

    public function testIsValid()
    {
        $this->assertFalse($this->action->isValid());

        $this->action->setTo(['test']);
        $this->assertFalse($this->action->isValid());

        $this->action->setFromName(['test']);
        $this->assertFalse($this->action->isValid());

        $this->action->setFile(['test']);
        $this->assertFalse($this->action->isValid());

        $this->action->setHash(['test']);
        $this->assertFalse($this->action->isValid());

        $this->action->setSize(1024);
        $this->assertFalse($this->action->isValid());

        $this->action->setUrl(['test']);
        $this->assertFalse($this->action->isValid());

        $this->action->setType(['test']);

        $this->assertTrue($this->action->isValid());
    }
}
