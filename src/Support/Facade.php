<?php

namespace Lanin\Laravel\ApiDebugger\Support;

use Lanin\Laravel\ApiDebugger\Debugger;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return Debugger::class;
    }
}
