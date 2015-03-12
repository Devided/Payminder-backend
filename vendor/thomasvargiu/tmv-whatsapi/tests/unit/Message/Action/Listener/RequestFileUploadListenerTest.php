<?php

namespace Tmv\WhatsApi\Message\Action\Listener;

use Tmv\WhatsApi\Message\Action\DirectMediaMessage;
use Mockery as m;

class RequestFileUploadListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestFileUploadListener
     */
    protected $listener;

    protected function setUp()
    {
        $this->listener = new RequestFileUploadListener();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testAttach()
    {
        $eventManagerMock = m::mock('Zend\EventManager\EventManagerInterface');
        $eventManagerMock->shouldReceive('attach')->once()
            ->with('action.send.pre', [$this->listener, 'onSendAction']);
        $eventManagerMock->shouldReceive('attach')->once()
            ->with('received.node.iq', [$this->listener, 'onReceivedNode']);

        $this->listener->attach($eventManagerMock);
    }

    public function testOnSendAction()
    {
        $nodeMock = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');
        $actionMock = m::mock('Tmv\WhatsApi\Message\Action\RequestFileUpload');
        $eventMock = m::mock('Zend\EventManager\EventInterface');
        $eventMock->shouldReceive('getParam')->with('action')->andReturn($actionMock);
        $eventMock->shouldReceive('getParam')->with('node')->andReturn($nodeMock);
        $actionMock->shouldReceive('getId')->andReturn(2);

        $this->assertCount(0, $this->listener->getMediaQueue());

        $this->listener->onSendAction($eventMock);

        $mediaQueue = $this->listener->getMediaQueue();
        $this->assertCount(1, $mediaQueue);
        $this->assertArrayHasKey(2, $mediaQueue);

        $item = $mediaQueue[2];
        $this->assertEquals(['action' => $actionMock, 'node' => $nodeMock], $item);
    }

    public function testOnSendActionNoRequestUpload()
    {
        $nodeMock = m::mock('Tmv\WhatsApi\Message\Node\NodeInterface');
        $actionMock = m::mock('Tmv\WhatsApi\Message\Action\ActionInterface');
        $eventMock = m::mock('Zend\EventManager\EventInterface');
        $eventMock->shouldReceive('getParam')->with('action')->andReturn($actionMock);
        $eventMock->shouldReceive('getParam')->with('node')->andReturn($nodeMock);

        $this->assertCount(0, $this->listener->getMediaQueue());

        $this->listener->onSendAction($eventMock);

        $this->assertCount(0, $this->listener->getMediaQueue());
    }

    public function testOnReceivedNodeWithMedia()
    {
        /** @var \Tmv\WhatsApi\Message\Node\Node $actionNode */
        $actionNode = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Message\Node\Node $node */
        $node = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Message\Node\Node $mediaNode */
        $mediaNode = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Entity\MediaFile $mediaFile */
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile[]');
        /** @var \Tmv\WhatsApi\Service\MediaService $mediaService */
        $mediaService = m::mock('Tmv\WhatsApi\Service\MediaService[uploadMediaFile,createImageIcon,createVideoIcon]');
        $mediaService->shouldReceive('createImageIcon')->andReturn(null);
        $mediaService->shouldReceive('createVideoIcon')->andReturn(null);
        $identity = m::mock('Tmv\WhatsApi\Entity\Identity');
        $identity->shouldReceive('getNickname')->andReturn('test-nickname');
        $client = m::mock('Tmv\WhatsApi\Client');
        $client->shouldReceive('getMediaService')->andReturn($mediaService);
        $client->shouldReceive('getIdentity')->andReturn($identity);
        /** @var \Tmv\WhatsApi\Message\Action\RequestFileUpload $action */
        $action = m::mock('Tmv\WhatsApi\Message\Action\RequestFileUpload[]');
        $eventMock = m::mock('Zend\EventManager\EventInterface');
        $eventMock->shouldReceive('getParam')->with('node')->andReturn($node);
        $eventMock->shouldReceive('getTarget')->with()->andReturn($client);

        $mediaFile->setType('image');

        $action->setMediaFile($mediaFile)
            ->setTo('test-address');

        $mediaNode->setName('media')
            ->setAttribute('url', 'http://test.com/image.jpg');

        $mediaService->shouldReceive('uploadMediaFile')
            ->with($mediaFile, $identity, $mediaNode->getAttribute('url'), $action->getTo())
            ->andReturn([
                'type' => 'image',
                'url' => 'test-url',
                'name' => 'test-name',
                'size' => 1024,
                'filehash' => 'test-hash',
            ]);

        $node->setAttribute('id', 'test-id')
            ->setAttribute('type', 'result');
        $node->addChild($mediaNode);

        $mediaQueue = [
            'test-id' => ['action' => $action, 'node' => $actionNode],
        ];
        $this->listener->setMediaQueue($mediaQueue);

        $client->shouldReceive('send')->once()->with(m::on(function ($action) {
            $expectations = [
                ($action instanceof DirectMediaMessage),
                ($action->getTo() === 'test-address'),
                ($action->getFromName() === 'test-nickname'),
                ($action->getFile() === 'test-name'),
                ($action->getUrl() === 'test-url'),
                ($action->getSize() == 1024),
                ($action->getHash() == 'test-hash'),
                ($action->getType() == 'image'),
            ];

            $result = array_filter(array_map(
                function ($item) {
                    return !$item;
                },
                $expectations
            ));

            return 0 === count($result);
        }));

        $this->listener->onReceivedNode($eventMock);

        $this->assertCount(0, $this->listener->getMediaQueue());
    }

    public function testOnReceivedNodeWithDuplicate()
    {
        /** @var \Tmv\WhatsApi\Message\Node\Node $actionNode */
        $actionNode = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Message\Node\Node $node */
        $node = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Message\Node\Node $mediaNode */
        $mediaNode = m::mock('Tmv\WhatsApi\Message\Node\Node[]');
        /** @var \Tmv\WhatsApi\Entity\MediaFile $mediaFile */
        $mediaFile = m::mock('Tmv\WhatsApi\Entity\MediaFile[]');
        $mediaService = m::mock('Tmv\WhatsApi\Service\MediaService[uploadMediaFile,createImageIcon,createVideoIcon]');
        $mediaService->shouldReceive('createImageIcon')->andReturn(null);
        $mediaService->shouldReceive('createVideoIcon')->andReturn(null);
        $identity = m::mock('Tmv\WhatsApi\Entity\Identity');
        $identity->shouldReceive('getNickname')->andReturn('test-nickname');
        $client = m::mock('Tmv\WhatsApi\Client');
        $client->shouldReceive('getMediaService')->andReturn($mediaService);
        $client->shouldReceive('getIdentity')->andReturn($identity);
        /** @var \Tmv\WhatsApi\Message\Action\RequestFileUpload $action */
        $action = m::mock('Tmv\WhatsApi\Message\Action\RequestFileUpload[]');
        $eventMock = m::mock('Zend\EventManager\EventInterface');
        $eventMock->shouldReceive('getParam')->with('node')->andReturn($node);
        $eventMock->shouldReceive('getTarget')->with()->andReturn($client);

        $mediaFile->setType('video');

        $action->setMediaFile($mediaFile)
            ->setTo('test-address');

        $mediaNode->setName('duplicate')
            ->setAttribute('url', 'http://test.com/image.jpg')
            ->setAttribute('type', 'video')
            ->setAttribute('size', 1024)
            ->setAttribute('name', 'test-name')
            ->setAttribute('filehash', 'test-hash');

        $node->setAttribute('id', 'test-id')
            ->setAttribute('type', 'result');
        $node->addChild($mediaNode);

        $mediaQueue = [
            'test-id' => ['action' => $action, 'node' => $actionNode],
        ];
        $this->listener->setMediaQueue($mediaQueue);

        $client->shouldReceive('send')->once()->with(m::on(function ($action) {
            $expectations = [
                ($action instanceof DirectMediaMessage),
                ($action->getTo() === 'test-address'),
                ($action->getFromName() === 'test-nickname'),
                ($action->getFile() === 'image.jpg'),
                ($action->getUrl() === 'http://test.com/image.jpg'),
                ($action->getSize() == 1024),
                ($action->getHash() == 'test-hash'),
                ($action->getType() == 'video'),
            ];

            $result = array_filter(array_map(
                function ($item) {
                    return !$item;
                },
                $expectations
            ));

            return 0 === count($result);
        }));

        $this->listener->onReceivedNode($eventMock);

        $this->assertCount(0, $this->listener->getMediaQueue());
    }
}
