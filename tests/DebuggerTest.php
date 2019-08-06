<?php

namespace Lanin\Laravel\ApiDebugger\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;

class DebuggerTest extends TestCase
{
    /** @test */
    public function it_can_dump_memory_usage()
    {
        $this->app['router']->get('foo', function () {
            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonStructure([
                'debug' => [
                    'memory' => [
                        'usage',
                        'peak',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_dump_var_via_helper()
    {
        $this->app['router']->get('foo', function () {
            lad('baz');

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonFragment([
                'dump' => [
                    'baz',
                ],
            ]);
    }

    /** @test */
    public function it_can_dump_multiple_vars()
    {
        $this->app['router']->get('foo', function () {
            lad('baz1', 'baz2');

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonFragment([
                'dump' => [
                    ['baz1', 'baz2'],
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
                                'connection',
                                'query',
                                'time',
                            ],
                        ],
                        'total',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_profile_custom_events()
    {
        $this->app['router']->get('foo', function () {
            lad_pr_start('test');
            usleep(300);
            lad_pr_stop('test');

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonStructure([
                'debug' => [
                    'profiling' => [
                        '*' => [
                            'event',
                            'time',
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'event' => 'test',
            ]);
    }

    /** @test */
    public function it_can_profile_simple_actions()
    {
        $this->app['router']->get('foo', function () {
            lad_pr_me('test', function () {
                usleep(300);
            });

            return response()->json(['foo' => 'bar']);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonStructure([
                'debug' => [
                    'profiling' => [
                        '*' => [
                            'event',
                            'time',
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'event' => 'test',
            ]);
    }

    /** @test */
    public function it_can_show_cache_events()
    {
        $this->app['router']->get('foo', function () {
            $value = Cache::tags('foo')->remember('bar', 60, function () {
                return 'bar';
            });

            $value = Cache::get('bar');

            return response()->json(['foo' => $value]);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertJsonStructure([
                'debug' => [
                    'cache' => [
                        'hit' => [
                            'keys',
                            'total',
                        ],
                        'miss' => [
                            'keys',
                            'total',
                        ],
                        'write' => [
                            'keys',
                            'total',
                        ],
                        'forget' => [
                            'keys',
                            'total',
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'miss' => [
                    'keys' => [
                        [
                            'tags' => ['foo'],
                            'key' => 'bar',
                        ],
                        'bar',
                    ],
                    'total' => 2,
                ],
            ]);
    }

    /** @test */
    public function it_preserves_object()
    {
        $this->app['router']->get('foo', function () {
            return response()->json([
                'foo' => 'bar',
                'baz' => (object)[],
            ]);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertSeeText('"baz":{}');
    }

    /** @test */
    public function it_preserves_array()
    {
        $this->app['router']->get('foo', function () {
            return response()->json([
                'foo' => 'bar',
                'baz' => [],
            ]);
        });

        $this->json('get', '/foo')
            ->assertStatus(200)
            ->assertSeeText('"baz":[]');
    }
}
