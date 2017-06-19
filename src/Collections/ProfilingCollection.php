<?php

namespace Lanin\Laravel\ApiDebugger\Collections;

use Illuminate\Contracts\Events\Dispatcher;
use Lanin\Laravel\ApiDebugger\Collection;
use Lanin\Laravel\ApiDebugger\Events\StopProfiling;
use Lanin\Laravel\ApiDebugger\Events\StartProfiling;

class ProfilingCollection implements Collection
{
    /**
     * @var Dispatcher
     */
    protected $events;

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
        $this->events = $dispatcher;

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
        $this->events->listen(StartProfiling::class, function (StartProfiling $event) {
			$this->started[$event->name] = microtime(true);
		});

        $this->events->listen(StopProfiling::class, function (StopProfiling $event) {
        	if (array_key_exists($event->name, $this->started)) {
        		$this->timers[] = [
        			'event' => $event->name,
        			'time' => microtime(true) - $this->started[$event->name],
				];
			}
		});
    }
}
