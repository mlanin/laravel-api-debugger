<?php

namespace Lanin\Laravel\ApiDebugger\Collections;

use Illuminate\Contracts\Events\Dispatcher;
use Lanin\Laravel\ApiDebugger\Collection;
use Lanin\Laravel\ApiDebugger\Events\StopProfiling;
use Lanin\Laravel\ApiDebugger\Events\StartProfiling;

class ProfilingCollection implements Collection
{
    const REQUEST_TIMER = 'request-time';

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $started = [];

    /**
     * @var array
     */
    protected $timers = [];

    /**
     * QueriesCollection constructor.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $this->listen();
    }

    /**
     * Collection name.
     *
     * @return string
     */
    public function name()
    {
        return 'profiling';
    }

    /**
     * Returns resulting collection.
     *
     * @return array
     */
    public function items()
    {
        return $this->timers;
    }

    /**
     * Listen query events.
     */
    public function listen()
    {
        if (defined('LARAVEL_START')) {
            $this->start(static::REQUEST_TIMER, LARAVEL_START);
        }

        $this->dispatcher->listen(StartProfiling::class, function (StartProfiling $event) {
            $this->start($event->name);
        });

        $this->dispatcher->listen(StopProfiling::class, function (StopProfiling $event) {
            $this->stop($event->name);
        });
    }

    /**
     * Start timer.
     *
     * @param string $name
     * @param null $time
     */
    protected function start($name, $time = null)
    {
        $this->started[$name] = $time ?: microtime(true);
    }

    /**
     * Stop timer.
     *
     * @param string $name
     */
    protected function stop($name)
    {
        if (array_key_exists($name, $this->started)) {
            $this->timers[] = [
                'event' => $name,
                'time' => microtime(true) - $this->started[$name],
            ];
        }
    }
}
