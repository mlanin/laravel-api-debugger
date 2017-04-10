<?php

namespace Lanin\Laravel\ApiDebugger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap application service.
     */
    public function boot()
    {
        // Listen to database queries and inject them to debug output.
        if ($this->app['config']['app.debug']) {
            $this->app->make(Debugger::class)
                ->populateWith($this->app->make(QueriesCollection::class));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Storage::class);
        $this->app->singleton(Debugger::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Storage::class,
            Debugger::class,
        ];
    }
}
