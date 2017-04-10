<?php

namespace Lanin\Laravel\ApiDebugger;

use Illuminate\Database\Connection;

class QueriesCollection implements Collection
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * QueriesCollection constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->listen();
    }

    /**
     * Collection name.
     *
     * @return string
     */
    public function name()
    {
        return 'database';
    }

    /**
     * Returns resulting collection.
     *
     * @return array
     */
    public function items()
    {
        return $this->queries;
    }

    /**
     * Listen query events.
     */
    public function listen()
    {
        $this->connection->enableQueryLog();

        $this->connection->listen(function ($event) {
            $this->logQuery($event->sql, $event->bindings, $event->time);
        });
    }

    /**
     * Log DB query.
     *
     * @param string $query
     * @param array $attributes
     * @param float $time
     */
    private function logQuery($query, $attributes, $time)
    {
        if (! empty($attributes)) {
            $query = vsprintf(str_replace(['%', '?'], ['%%', "'%s'"], $query), $attributes) . ';';
        }

        $this->queries[] = [
            'query' => $query,
            'time'  => $time,
        ];
    }
}
