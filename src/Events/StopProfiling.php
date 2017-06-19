<?php

namespace Lanin\Laravel\ApiDebugger\Events;

class StopProfiling
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 *  StopProfiling constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}
}