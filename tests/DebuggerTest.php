<?php

namespace Lanin\Laravel\ApiDebugger\Tests;

use Illuminate\Database\Schema\Blueprint;

class DebuggerTest extends TestCase
{
    /** @test */
    public function it_can_dump_var_via_helper()
    {
        $this->app['router']->get('foo', function () {
            lad('baz');

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertExactJson([
                'foo' => 'bar',
                'debug' => [
                    'dump' => [
                        'baz',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_dump_query()
    {
        $this->app['router']->get('foo', function () {
            \Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
            });

            \DB::table('users')->get();

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonStructure([
                'debug' => [
                    'database' => [
                        'items' => [
                            '*' => [
                                'query',
                                'time',
                            ],
                        ],
                        'total',
                    ],
                ],
            ]);
    }
}
