<?php

namespace Lanin\Laravel\ApiDebugger\Collections;

use Lanin\Laravel\ApiDebugger\Collection;

class MemoryCollection implements Collection
{
    /**
     * Collection name.
     *
     * @return string
     */
    public function name()
    {
        return 'memory';
    }

    /**
     * Returns resulting collection.
     *
     * @return array
     */
    public function items()
    {
        return [
            'usage' => memory_get_usage(),
            'peak' => memory_get_peak_usage(),
        ];
    }
}
