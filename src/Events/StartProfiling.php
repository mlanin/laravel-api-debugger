<?php

namespace Lanin\Laravel\ApiDebugger\Events;

class StartProfiling
{
    /**
     * @var string
     */
    public $name;

    /**
     *  StartProfiling constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
}
