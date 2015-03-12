<?php

namespace Tmv\WhatsApi\Connection\Adapter;

interface AdapterInterface
{
    /**
     * Connect
     *
     * @return $this
     */
    public function connect();

    /**
     * Disconnect
     *
     * @return $this
     */
    public function disconnect();

    /**
     * Send data
     *
     * @param  string $data
     * @return $this
     */
    public function sendData($data);

    /**
     * Read data
     * @return string
     */
    public function readData();
}
