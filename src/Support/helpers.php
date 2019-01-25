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
