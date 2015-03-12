<?php

namespace Tmv\WhatsApi\Connection\Adapter;

use RuntimeException;

class SocketAdapter implements AdapterInterface
{
    /**
     * @var resource
     */
    protected $socket;
    /**
     * @var string
     */
    protected $hostname;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var int
     */
    protected $timeoutSec = 2;
    /**
     * @var int
     */
    protected $timeoutUsec = 0;
    /**
     * @var string
     */
    protected $incompleteMessage;

    /**
     * @param  resource $socket
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @return resource
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param  string $hostname
     * @return $this
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param  int   $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param  int   $timeoutSec
     * @return $this
     */
    public function setTimeoutSec($timeoutSec)
    {
        $this->timeoutSec = $timeoutSec;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeoutSec()
    {
        return $this->timeoutSec;
    }

    /**
     * @param  int   $timeoutUsec
     * @return $this
     */
    public function setTimeoutUsec($timeoutUsec)
    {
        $this->timeoutUsec = $timeoutUsec;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeoutUsec()
    {
        return $this->timeoutUsec;
    }

    /**
     * Connect
     *
     * @return $this
     * @throws RuntimeException
     */
    public function connect()
    {
        $socket = fsockopen($this->getHostname(), $this->getPort());
        if ($socket !== false) {
            stream_set_timeout($socket, $this->getTimeoutSec(), $this->getTimeoutUsec());
            $this->socket = $socket;
        } else {
            throw new RuntimeException("Unable to connect");
        }

        return $this;
    }

    /**
     * Disconnect
     *
     * @return $this
     */
    public function disconnect()
    {
        if (null !== $this->socket) {
            @fclose($this->socket);
            $this->socket = null;
        }

        return $this;
    }

    /**
     * Send data
     *
     * @param  string $data
     * @return $this
     */
    public function sendData($data)
    {
        fwrite($this->socket, $data, strlen($data));

        return $this;
    }

    /**
     * Read 1024 bytes from the whatsapp server.
     */
    public function readData()
    {
        return $this->readStanza();
    }

    /**
     * Read 1024 bytes from the whatsapp server.
     */
    public function readStanza()
    {
        if ($this->socket != null) {
            $header = @fread($this->socket, 3); //read stanza header
            if (strlen($header) == 0) {
                //no data received
                return '';
            }
            if (strlen($header) != 3) {
                throw new RuntimeException("Failed to read stanza header");
            }
            $treeLength = ord($header[1]) << 8;
            $treeLength |= ord($header[2]) << 0;

            //read full length
            $buff = @fread($this->socket, $treeLength);
            $len = strlen($buff);
            while (strlen($buff) < $treeLength) {
                $toRead = $treeLength - strlen($buff);
                $buff .= @fread($this->socket, $toRead);
                if ($len == strlen($buff)) {
                    //no new data read, fuck it
                    break;
                }
                $len = strlen($buff);
            }

            if (strlen($buff) != $treeLength) {
                throw new RuntimeException("Tree length did not match received length (buff = ".strlen($buff)." & treeLength = $treeLength)");
            } elseif (@feof($this->socket)) {
                @fclose($this->socket);
                $this->socket = null;
                throw new RuntimeException("Socket EOF, connection closed");
            }
            $buff = $header.$buff;
        } else {
            throw new RuntimeException("Socket closed");
        }

        return $buff;
    }
}
