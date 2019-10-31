<?php

return [
    'enabled' => (bool) env('API_DEBUGGER_ENABLED', env('APP_DEBUG', false)),
    /**
     * Specify what data to collect.
     */
    'collections' => [
        // Database queries.
        \Lanin\Laravel\ApiDebugger\Collections\QueriesCollection::class,

        // Show cache events.
        \Lanin\Laravel\ApiDebugger\Collections\CacheCollection::class,

        // Profile custom events.
        \Lanin\Laravel\ApiDebugger\Collections\ProfilingCollection::class,

        // Memory usage.
        \Lanin\Laravel\ApiDebugger\Collections\MemoryCollection::class,
    ],

    'response_key' => env('API_DEBUGGER_KEY', 'debug')
];
