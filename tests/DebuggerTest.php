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
			->assertJsonFragment([
				'dump' => [
					'baz',
				]
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


}
