<?php

namespace Lanin\Laravel\ApiDebugger;

interface Collection
{
    /**
     * Collection name.
     *
     * @return string
     */
    public function name();

    /**
     * Returns resulting collection.
     *
     * @return array
     */
    public function items();
}
