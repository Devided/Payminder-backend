<?php

namespace Tmv\WhatsApi\Service;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

class PcntlListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var bool
     */
    protected $shouldStop = false;

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
        $this->listeners[] = $events->attach('run.start', [$this, 'installSignalHandlers']);
        $this->listeners[] = $events->attach('run', [$this, 'checkSignals']);
    }

    /**
     *
     */
    public function installSignalHandlers()
    {
        if (!function_exists('pcntl_signal')) {
            throw new \RuntimeException("PCNTL extension seems not installed");
        }
        $this->shouldStop = false;
        pcntl_signal(SIGTERM, [$this, 'signalHandler']);
        pcntl_signal(SIGINT, [$this, 'signalHandler']);
        pcntl_signal(SIGUSR1, [$this, 'signalHandler']);
    }

    /**
     * @param EventInterface $event
     */
    public function checkSignals(EventInterface $event)
    {
        pcntl_signal_dispatch();
        if ($this->shouldStop) {
            $event->stopPropagation();
        }
    }

    // signal handler function
    public function signalHandler($signal)
    {
        switch ($signal) {
            case SIGTERM:
            case SIGINT:
            case SIGUSR1:
                $this->shouldStop = true;
                break;
        }
    }
}
