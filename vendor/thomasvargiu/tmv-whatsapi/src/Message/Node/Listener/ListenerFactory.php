<?php

namespace Tmv\WhatsApi\Message\Node\Listener;

use InvalidArgumentException;
use RuntimeException;
use Zend\EventManager\ListenerAggregateInterface;

class ListenerFactory
{
    /**
     * @param  string                     $name
     * @return ListenerAggregateInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function factory($name)
    {
        $name = ucfirst($name);
        $className = __NAMESPACE__.'\\'.$name.'Listener';

        if (!class_exists($className)) {
            throw new InvalidArgumentException('Missing listener class.');
        }

        $instance = new $className();
        if (!$instance instanceof ListenerAggregateInterface) {
            throw new RuntimeException(sprintf("Listener '%s' is not valid", $name));
        }

        return $instance;
    }
}
