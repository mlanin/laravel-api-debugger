<?php

namespace Lanin\Laravel\ApiDebugger\Tests;

use Illuminate\Database\Connection;
use Lanin\Laravel\ApiDebugger\Collections\QueriesCollection;

class QueriesCollectionTest extends TestCase
{
    public function pdoBindingsProvider()
    {
        return [
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = %;",
                'attributes' => [
                    'foo',
                ],
                'result' => "select * from `users` where name = %;",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    'foo',
                ],
                'result' => "select * from `users` where name = 'foo';",
            ]
        ];
    }

    /**
     * @test
     * @dataProvider pdoBindingsProvider
     *
     * @param string $connection
     * @param string $query
     * @param array $attributes
     * @param string $result
     */
    public function it_handles_testing_pdo_bindings($connection, $query, $attributes, $result)
    {
        $collection = $this->factory();

        $collection->logQuery($connection, $query, $attributes, 1);
        $this->assertEquals($result, $collection->items()['items'][0]['query']);
    }

    public function mixedAttributesProvider()
    {
        return [
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    1,
                ],
                'result' => "select * from `users` where name = '1';",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    true,
                ],
                'result' => "select * from `users` where name = '1';",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    ['foo'],
                ],
                'result' => "select * from `users` where name = '[\"foo\"]';",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    function () {
                        return 'foo';
                    },
                ],
                'result' => "select * from `users` where name = 'foo';",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    new \DateTime('2017-04-24'),
                ],
                'result' => "select * from `users` where name = '2017-04-24 00:00:00';",
            ],
            [
                'connection' => 'testing',
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    new Foo(),
                ],
                'result' => "select * from `users` where name = 'Lanin\Laravel\ApiDebugger\Tests\Foo';",
            ],
        ];
    }

    /**
     * @test
     * @dataProvider mixedAttributesProvider
     *
     * @param string $connection
     * @param string $query
     * @param array $attributes
     * @param string $result
     */
    public function it_handles_mixed_attributes_types($connection, $query, $attributes, $result)
    {
        $collection = $this->factory();

        $collection->logQuery($connection, $query, $attributes, 1);
        $this->assertEquals($result, $collection->items()['items'][0]['query']);
    }

    public function factory()
    {
        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive('enableQueryLog');
        $connection->shouldReceive('listen');

        return new QueriesCollection($connection);
    }
}

class Foo {

}