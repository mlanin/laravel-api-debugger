<?php

namespace Lanin\Laravel\ApiDebugger;

class Storage
{
    /**
     * @var array
     */
    protected $dump = [];

    /**
     * @var Collection[]
     */
    protected $collections = [];

    /**
     * Set collection.
     *
     * @param Collection $collection
     */
    public function inject(Collection $collection)
    {
        $this->collections[] = $collection;
    }

    /**
     * Add vars to debug output.
     *
     * @param array $vars
     */
    public function dump($vars)
    {
        $this->dump[] = count($vars) == 1
            ? $vars[0]
            : $vars;
    }

    /**
     * If storage is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->collections) == 0 && count($this->dump) == 0;
    }

    /**
     * Return result debug data.
     *
     * @return array
     */
    public function getData()
    {
        $return = [];

        foreach ($this->collections as $collection) {
            $return[$collection->name()] = $collection->items();
        }

        if (count($this->dump) != 0) {
            $return['dump'] = $this->dump;
        }

        return $return;
    }
}
