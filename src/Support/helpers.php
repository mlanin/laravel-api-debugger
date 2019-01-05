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

if (! function_exists('lad_pr_start')) {
    /**
     * Start profiling event.
     *
     * @param  string $name
     * @return void
     */
    function lad_pr_start($name)
    {
        app(Debugger::class)->startProfiling($name);
    }
}

if (! function_exists('lad_pr_stop')) {
    /**
     * Finish profiling event.
     *
     * @param  string $name
     * @return void
     */
    function lad_pr_stop($name)
    {
        app(Debugger::class)->stopProfiling($name);
    }
}

if (! function_exists('lad_pr_me')) {
    /**
     * Finish profiling event.
     *
     * @param  string $name
     * @param  Closure|null $action
     * @return mixed
     */
    function lad_pr_me($name, \Closure $action = null)
    {
        return app(Debugger::class)->profileMe($name, $action);
    }
}


if (! function_exists('convert_to_unit')) {
    /**
     * Convert to unit ('b', 'kb', 'mb', 'gb', 'tb', 'pb')
     * 
     * @param string $size
     * @return mixed
     */
    function convert_to_unit($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
}
