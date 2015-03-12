<?php

namespace Tmv\WhatsApi\Connection\Adapter;

class SocketAdapterFactory
{
    /**
     * Factory
     *
     * @param  array         $config
     * @return SocketAdapter
     */
    public static function factory(array $config)
    {
        $defaults = [
            'hostname' => 'c.whatsapp.net',
            'port' => 443,
            'timeout_sec' => 2,
            'timeout_usec' => 0,
        ];
        $config = array_merge($defaults, $config);
        $adapter = new SocketAdapter();
        $adapter->setHostname($config['hostname']);
        $adapter->setPort($config['port']);
        $adapter->setTimeoutSec($config['timeout_sec']);
        $adapter->setTimeoutUsec($config['timeout_usec']);

        return $adapter;
    }
}
