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
        $this->dump = array_merge($this->dump, $vars);
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
            $items = $collection->items();

            if ($total = count($items)) {
                $return[$collection->name()] = [
                    'total' => $total,
                    'items' => $items,
                ];
            }
        }

        if (count($this->dump) != 0) {
            $return['dump'] = $this->dump;
        }

        return $return;
    }
}
