<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use Tmv\WhatsApi\Message\Node\Node;
use Tmv\WhatsApi\Message\Node\NodeInterface;
use Tmv\WhatsApi\Protocol\KeyStream;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Tmv\WhatsApi\Client;
use Tmv\WhatsApi\Connection\Connection;
use Tmv\WhatsApi\Entity\Identity;

class ChallengeListener extends AbstractListener
{
    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('received.node.challenge', [$this, 'onReceivedNode']);
    }

    /**
     * @param EventInterface $e
     */
    public function onReceivedNode(EventInterface $e)
    {
        /** @var NodeInterface $node */
        $node = $e->getParam('node');
        /** @var Client $client */
        $client = $e->getTarget();

        $challengeData = $node->getData();
        $client->setChallengeData($challengeData);

        if (!$challengeData) {
            return;
        }

        $connection = $client->getConnection();
        $identity = $client->getIdentity();

        $data = $this->createAuthResponseNode($connection, $identity, $challengeData);
        $client->sendNode($data);
        $connection->getNodeReader()->setKey($connection->getInputKey());
        $connection->getNodeWriter()->setKey($connection->getOutputKey());
    }

    /**
     * Add the auth response
     *
     * @param  Connection    $connection
     * @param  Identity      $identity
     * @param  string        $challengeData
     * @return NodeInterface
     */
    protected function createAuthResponseNode(Connection $connection, Identity $identity, $challengeData)
    {
        $resp = $this->getAuthData($connection, $identity, $challengeData);
        $respHash = [];
        $respHash["xmlns"] = "urn:ietf:params:xml:ns:xmpp-sasl";

        $node = Node::fromArray(
            [
                'name' => 'response',
                'attributes' => $respHash,
                'data' => $resp,
            ]
        );

        return $node;
    }

    /**
     * Authenticate with the Whatsapp Server.
     *
     * @param  Connection $connection
     * @param  Identity   $identity
     * @param  string     $challengeData
     * @return string     Returns binary string
     */
    protected function getAuthData(Connection $connection, Identity $identity, $challengeData)
    {
        $keys = KeyStream::generateKeys(base64_decode($identity->getPassword()), $challengeData);
        $connection->setInputKey($this->createKeyStream($keys[2], $keys[3]));
        $connection->setOutputKey($this->createKeyStream($keys[0], $keys[1]));
        $array = "\0\0\0\0".$identity->getPhone()->getPhoneNumber().$challengeData;
        $response = $connection->getOutputKey()->encodeMessage($array, 0, 4, strlen($array) - 4);

        return $response;
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
}
