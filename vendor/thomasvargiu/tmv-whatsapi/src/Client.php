<?php

namespace Tmv\WhatsApi;

use Tmv\WhatsApi\Connection\Adapter\SocketAdapterFactory;
use Tmv\WhatsApi\Connection\Connection;
use Tmv\WhatsApi\Entity\Identity;
use Tmv\WhatsApi\Exception\RuntimeException;
use Tmv\WhatsApi\Message\Action;
use Tmv\WhatsApi\Message\Node\Listener\ListenerFactory;
use Tmv\WhatsApi\Message\Node\Node;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Tmv\WhatsApi\Protocol\KeyStream;
use Tmv\WhatsApi\Service\MediaService;
use Tmv\WhatsApi\Service\ProtocolService;
use Zend\EventManager\EventManager;

/**
 * Class Client
 * @package Tmv\WhatsApi
 */
class Client
{
    const PORT = 443; // The port of the WhatsApp server.
    const TIMEOUT_SEC = 2; // The timeout for the connection with the WhatsApp servers.
    const TIMEOUT_USEC = 0; //
    const WHATSAPP_CHECK_HOST = 'v.whatsapp.net/v2/exist'; // The check credentials host.
    const WHATSAPP_GROUP_SERVER = 'g.us'; // The Group server hostname
    const WHATSAPP_HOST = 'c.whatsapp.net'; // The hostname of the WhatsApp server.
    const WHATSAPP_REGISTER_HOST = 'v.whatsapp.net/v2/register'; // The register code host.
    const WHATSAPP_REQUEST_HOST = 'v.whatsapp.net/v2/code'; // The request code host.
    const WHATSAPP_SERVER = 's.whatsapp.net'; // The hostname used to login/send messages.
    const WHATSAPP_UPLOAD_HOST = 'https://mms.whatsapp.net/client/iphone/upload.php'; // The upload host.
    const WHATSAPP_DEVICE = 'iPhone'; // The device name.
    const WHATSAPP_VER = '2.11.14';                // The WhatsApp version.
    const WHATSAPP_USER_AGENT = 'WhatsApp/2.12.61 S40Version/14.26 Device/Nokia302';// User agent used in request/registration code.

    /**
     * @var bool
     */
    protected $connected = false;
    /**
     * @var EventManager
     */
    protected $eventManager;
    /**
     * @var ProtocolService
     */
    protected $protocolService;
    /**
     * @var string
     */
    protected $challengeData;

    /**
     * @var string
     */
    protected $challengeDataFilepath;

    /**
     * @var Identity
     */
    protected $identity;
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * Default class constructor.
     *
     * @param Identity $identity
     */
    public function __construct(Identity $identity)
    {
        $listenerFactory = new ListenerFactory();
        $this->getEventManager()->attachAggregate($listenerFactory->factory('StreamError', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Notification', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Challenge', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Success', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Failure', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Message', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Receipt', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Presence', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('ChatState', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Iq', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('Ib', $this), 100);
        $this->getEventManager()->attachAggregate($listenerFactory->factory('InjectId', $this), 100);

        $this->getEventManager()->attachAggregate(new Action\Listener\RequestFileUploadListener(), 100);

        $this->setIdentity($identity);
        $this->setConnected(false);
    }

    /**
     * @return MediaService
     */
    public function getMediaService()
    {
        if (!$this->mediaService) {
            $this->mediaService = new MediaService();
        }

        return $this->mediaService;
    }

    /**
     * @param  MediaService $mediaService
     * @return $this
     */
    public function setMediaService(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;

        return $this;
    }

    /**
     * Get the event manager
     *
     * @param EventManager $manager
     *
     * @return EventManager
     */
    public function getEventManager(EventManager $manager = null)
    {
        if (null !== $manager) {
            $this->eventManager = $manager;
        } elseif (null === $this->eventManager) {
            $this->eventManager = new EventManager(__CLASS__);
        }

        return $this->eventManager;
    }

    /**
     * @param  EventManager $eventManager
     * @return $this
     */
    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * Connect (create a socket) to the WhatsApp network.
     * @param  bool   $login Automatically login
     * @return $this;
     */
    public function connect($login = true)
    {
        $this->getConnection()->connect();
        if ($login) {
            $this->login();
        }

        return $this;
    }

    /**
     * Disconnect to the WhatsApp network.
     *
     * @return $this
     */
    public function disconnect()
    {
        $this->getConnection()->disconnect();

        return $this;
    }

    /**
     * Set the connection status with the WhatsApp server
     *
     * @param  boolean $connected
     * @return $this
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * Get the connection status with the WhatsApp server
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param  ProtocolService $protocolService
     * @return $this
     */
    public function setProtocolService($protocolService)
    {
        $this->protocolService = $protocolService;

        return $this;
    }

    /**
     * @return ProtocolService
     */
    public function getProtocolService()
    {
        if (!$this->protocolService) {
            $this->protocolService = new ProtocolService();
        }

        return $this->protocolService;
    }

    /**
     * Login to the Whatsapp server with your password
     *
     * If you already know your password you can log into the Whatsapp server
     * using this method.
     *
     * @throws RuntimeException
     */
    public function login()
    {
        $challengeData = $this->readChallengeData();
        if (!empty($challengeData)) {
            $this->challengeData = $challengeData;
        }
        $this->doLogin();

        return $this;
    }

    /**
     * Send the nodes to the Whatsapp server to log in.
     */
    protected function doLogin()
    {
        $this->getConnection()->getNodeWriter()->resetKey();
        $this->getConnection()->getNodeReader()->resetKey();
        $resource = static::WHATSAPP_DEVICE.'-'.static::WHATSAPP_VER.'-'.static::PORT;
        $data = $this->getConnection()->getNodeWriter()->startStream(static::WHATSAPP_SERVER, $resource);

        $this->sendData($data);

        $this->sendNode(Node::fromArray(
            ['name' => 'stream:features']
        ));

        $auth = $this->createAuthNode();
        $this->sendNode($auth);
    }

    /**
     * Send an action to the WhatsApp server.
     *
     * @param  Action\ActionInterface $action
     * @return $this
     */
    public function send(Action\ActionInterface $action)
    {
        $node = $action->createNode();

        $argv = compact('action', 'node');
        $eventParams = $this->getEventManager()->prepareArgs($argv);
        $results = $this->getEventManager()->trigger('action.send.pre', $this, $eventParams);
        if ($results->stopped()) {
            return $this;
        }

        /** @var Action\ActionInterface $action */
        $action = $argv['action'];
        $node = $argv['node'];

        if (!$action->isValid()) {
            throw new \RuntimeException(
                sprintf("Action is not valid or missing parameters for action '%s'", get_class($action))
            );
        }

        $node = $this->sendNode($node);

        $eventParams = ['action' => $action, 'node' => $node];
        $this->getEventManager()->trigger('action.send.post', $this, $eventParams);

        return $this;
    }

    /**
     * Send node to the WhatsApp server.
     * @param  NodeInterface $node
     * @param  bool          $encrypt
     * @return NodeInterface
     */
    public function sendNode(NodeInterface $node, $encrypt = true)
    {
        $argv = compact('node');
        $argv = $this->getEventManager()->prepareArgs($argv);
        $this->getEventManager()->trigger('node.send.pre', $this, $argv);
        $node = $argv['node'];

        $data = $this->getConnection()->getNodeWriter()->write($node, $encrypt);
        $this->sendData($data);

        $this->getEventManager()->trigger('node.send.post', $this, $argv);

        return $node;
    }

    /**
     * Send data to the whatsapp server.
     * @param  string $data
     * @return $this
     */
    protected function sendData($data)
    {
        $this->getConnection()->sendData($data);

        return $this;
    }

    /**
     * Pull from the socket, and place incoming messages in the message queue.
     *
     * @param  bool $autoReceipt
     * @return bool
     */
    public function pollMessages($autoReceipt = true)
    {
        $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);

        $data = $this->getConnection()->readData();
        if ($data) {
            $this->processInboundData($data, $autoReceipt);
        }

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this);

        return !empty($data);
    }

    /**
     * Connect, Login and start loop for reading data
     *
     * @param  bool  $sendPresence Automatically send presence
     * @return $this
     */
    public function run($sendPresence = true)
    {
        $this->getEventManager()->trigger(__FUNCTION__.'.start', $this);
        $this->connect(true);
        $time = time();
        $stopped = false;
        while (!$stopped) {
            $this->pollMessages();
            if ($sendPresence && (time() - $time >= 10)) {
                $time = time();
                $this->send(new Action\Presence($this->getIdentity()->getNickname()));
            }
            $results = $this->getEventManager()->trigger(__FUNCTION__, $this);
            $stopped = $results->stopped();
            usleep(1000);
        }

        $this->getEventManager()->trigger(__FUNCTION__.'.stop', $this);

        $this->getConnection()->disconnect();

        return $this;
    }

    /**
     * Process inbound data.
     * @param  bool   $autoReceipt
     * @param  string $data        The data to process.
     * @return $this
     */
    protected function processInboundData($data, $autoReceipt = true)
    {
        $node = $this->getConnection()->getNodeReader()->nextTree($data);
        if ($node) {
            $this->getEventManager()->trigger('node.received',
                $this,
                ['node' => $node, 'autoReceipt' => $autoReceipt]
            );
            $params = ['node' => $node];
            $this->getEventManager()->trigger('received.node.'.$node->getName(), $this, $params);
        }

        return $this;
    }

    /**
     * Add the authentication nodes.
     *
     * @return NodeInterface
     *                       Return itself.
     */
    protected function createAuthNode()
    {
        $authHash = [];
        $authHash["xmlns"] = "urn:ietf:params:xml:ns:xmpp-sasl";
        $authHash["mechanism"] = "WAUTH-2";
        $authHash["user"] = $this->getIdentity()->getPhone()->getPhoneNumber();
        $data = $this->createAuthBlob();

        $node = Node::fromArray(
            [
                'name' => 'auth',
                'attributes' => $authHash,
                'data' => $data,
            ]
        );

        return $node;
    }

    /**
     * Create a keystream
     *
     * @param  string    $key
     * @param  string    $macKey
     * @return KeyStream
     */
    protected function createKeyStream($key, $macKey)
    {
        return new KeyStream($key, $macKey);
    }

    protected function createAuthBlob()
    {
        if ($this->challengeData) {
            $key = ProtocolService::pbkdf2('sha1', base64_decode($this->getIdentity()->getPassword()), $this->challengeData, 16, 20, true);
            $this->getConnection()->setInputKey($this->createKeyStream($key[2], $key[3]));
            $this->getConnection()->setOutputKey($this->createKeyStream($key[0], $key[1]));
            $this->getConnection()->getNodeReader()->setKey($this->getConnection()->getInputKey());
            //$this->getConnection()->getNodeWriter()->setKey($this->getConnection()->getOutputKey());
            $phone = $this->getIdentity()->getPhone();
            $array = "\0\0\0\0".
                $phone->getPhoneNumber().
                $this->challengeData.
                time().
                static::WHATSAPP_USER_AGENT.
                " MccMnc/".
                str_pad($phone->getMcc(), 3, "0", STR_PAD_LEFT).
                "001";

            $this->challengeData = null;

            return $this->getConnection()->getOutputKey()->encodeMessage($array, 0, strlen($array), 0);
        }

        return null;
    }

    /**
     * @param  string $challengeData
     * @return $this
     */
    public function setChallengeData($challengeData)
    {
        $this->challengeData = $challengeData;

        return $this;
    }

    /**
     * @return string
     */
    public function getChallengeData()
    {
        return $this->challengeData;
    }

    /**
     * @param  string $filePath
     * @return $this
     */
    public function setChallengeDataFilepath($filePath)
    {
        $this->challengeDataFilepath = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getChallengeDataFilepath()
    {
        return $this->challengeDataFilepath;
    }

    /**
     * @param  string $data
     * @return $this
     */
    public function writeChallengeData($data)
    {
        $this->checkChallengeDataFilePermission();
        $filepath = $this->getChallengeDataFilepath();
        file_put_contents($filepath, $data);

        return $this;
    }

    /**
     * @return string
     */
    public function readChallengeData()
    {
        $this->checkChallengeDataFilePermission();
        $filepath = $this->getChallengeDataFilepath();

        return file_get_contents($filepath);
    }

    /**
     * @return bool
     * @throws \Tmv\WhatsApi\Exception\RuntimeException
     */
    public function checkChallengeDataFilePermission()
    {
        $filePath = $this->getChallengeDataFilepath();
        if (!$filePath) {
            throw new RuntimeException("Filename for challenge data is not setted");
        }
        $baseDir = dirname($filePath);
        if (!file_exists($baseDir)) {
            throw new RuntimeException(sprintf("Directory '%s' doesn't exists", $baseDir));
        } elseif (!file_exists($filePath) && !is_writable($baseDir)) {
            throw new RuntimeException(sprintf("Directory '%s' is not writable", $baseDir));
        } elseif (!file_exists($filePath)) {
            touch($filePath);
        }

        if (!is_writable($filePath)) {
            throw new RuntimeException(sprintf("File '%s' is not writable", $filePath));
        }

        return true;
    }

    /**
     * @param  \Tmv\WhatsApi\Connection\Connection $connection
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return \Tmv\WhatsApi\Connection\Connection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $adapter = SocketAdapterFactory::factory([
                'hostname' => static::WHATSAPP_HOST,
                'port' => static::PORT,
            ]);
            $connection = new Connection($adapter);
            $this->connection = $connection;
        }

        return $this->connection;
    }

    /**
     * @param  \Tmv\WhatsApi\Entity\Identity $identity
     * @return $this
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @return \Tmv\WhatsApi\Entity\Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }
}
