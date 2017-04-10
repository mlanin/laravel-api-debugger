<?php

use Lanin\Laravel\ApiDebugger\Debugger;

if (! function_exists('lad')) {
    /**
     * Dump the passed variables.
     *
     * @param  mixed
     * @return void
     */
    function lad()
    {
        call_user_func_array([app(Debugger::class), 'dump'], func_get_args());
    }
}
