<?php

namespace Lanin\Laravel\ApiDebugger\Tests;

use Illuminate\Database\Connection;
use Lanin\Laravel\ApiDebugger\QueriesCollection;

class QueriesCollectionTest extends TestCase
{
    public function pdoBindingsProvider()
    {
        return [
            [
                'query' => "select * from `users` where name = %;",
                'attributes' => [
                    'foo',
                ],
                'result' => "select * from `users` where name = %;",
            ],
            [
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
     * @param string $query
     * @param array $attributes
     * @param string $result
     */
    public function it_handles_default_pdo_bindings($query, $attributes, $result)
    {
        $collection = $this->factory();

        $collection->logQuery($query, $attributes, 1);
        $this->assertEquals($result, $collection->items()[0]['query']);
    }

    public function mixedAttributesProvider()
    {
        return [
            [
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    1,
                ],
                'result' => "select * from `users` where name = '1';",
            ],
            [
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    true,
                ],
                'result' => "select * from `users` where name = '1';",
            ],
            [
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    ['foo'],
                ],
                'result' => "select * from `users` where name = '[\"foo\"]';",
            ],
            [
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    function () {
                        return 'foo';
                    },
                ],
                'result' => "select * from `users` where name = 'foo';",
            ],
            [
                'query' => "select * from `users` where name = ?;",
                'attributes' => [
                    new \DateTime('2017-04-24'),
                ],
                'result' => "select * from `users` where name = '2017-04-24 00:00:00';",
            ],
            [
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
     * @param string $query
     * @param array $attributes
     * @param string $result
     */
    public function it_handles_mixed_attributes_types($query, $attributes, $result)
    {
        $collection = $this->factory();

        $collection->logQuery($query, $attributes, 1);
        $this->assertEquals($result, $collection->items()[0]['query']);
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