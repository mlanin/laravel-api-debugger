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
            'usage' => $this->convertToUnit(memory_get_usage()),
            'peak' => $this->convertToUnit(memory_get_peak_usage()),
        ];
    }

    /**
     * Convert to unit ('b', 'kb', 'mb', 'gb', 'tb', 'pb')
     * 
     * @param string $size
     * @return mixed
     */
    public function convertToUnit($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
}
