<?php

namespace Lanin\Laravel\ApiDebugger\Tests;

use Lanin\Laravel\ApiDebugger\Debugger;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_can_get_provide_compiler()
    {
        $this->assertInstanceOf(Debugger::class, $this->app[Debugger::class]);
    }
}
