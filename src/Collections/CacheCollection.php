<?php

namespace Lanin\Laravel\ApiDebugger\Collections;

use Illuminate\Cache\Events\CacheEvent;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Contracts\Events\Dispatcher;
use Lanin\Laravel\ApiDebugger\Collection;

class CacheCollection implements Collection
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $events = [
		'hit' => ['keys' => [], 'total' => 0],
		'miss' => ['keys' => [], 'total' => 0],
		'write' => ['keys' => [], 'total' => 0],
		'forget' => ['keys' => [], 'total' => 0],
	];

    /**
     * CacheCollection constructor.
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
        return 'cache';
    }

    /**
     * Returns resulting collection.
     *
     * @return array
     */
    public function items()
    {
        return $this->events;
    }

    /**
     * Listen query events.
     */
    public function listen()
    {
        $this->dispatcher->listen(CacheHit::class, [$this, 'hit']);
        $this->dispatcher->listen(CacheMissed::class, [$this, 'miss']);
        $this->dispatcher->listen(KeyWritten::class, [$this, 'write']);
        $this->dispatcher->listen(KeyForgotten::class, [$this, 'forget']);
    }

	/**
	 * Store hit.
	 *
	 * @param CacheHit $event
	 */
    public function hit(CacheHit $event)
	{
		$this->store(__FUNCTION__, $event);
	}

	/**
	 * Store miss.
	 *
	 * @param CacheMissed $event
	 */
	public function miss(CacheMissed $event)
	{
		$this->store(__FUNCTION__, $event);
	}

	/**
	 * Store write.
	 *
	 * @param KeyWritten $event
	 */
	public function write(KeyWritten $event)
	{
		$this->store(__FUNCTION__, $event);
	}

	/**
	 * Store forget.
	 *
	 * @param KeyForgotten $event
	 */
	public function forget(KeyForgotten $event)
	{
		$this->store(__FUNCTION__, $event);
	}

	/**
	 * Store event.
	 *
	 * @param string $label
	 * @param CacheEvent $event
	 */
    protected function store($label, CacheEvent $event)
	{
		$tags = $event->tags;

		$this->events[$label]['keys'][] = !empty($tags)
			? ['tags' => $tags, 'key' => $event->key]
			: $event->key;

		$this->events[$label]['total']++;
	}
}
