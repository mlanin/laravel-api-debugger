<?php

namespace Lanin\Laravel\ApiDebugger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap application service.
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/api-debugger.php';
        $this->publishes([$configPath => config_path('api-debugger.php')]);
        $this->mergeConfigFrom($configPath, 'api-debugger');

        // Register collections only for debug environment.
        $config = $this->app['config'];
        if ($config['api-debugger.enabled']) {
            $this->registerCollections($config['api-debugger.collections']);

            $this->setResponseKey($config['api-debugger.response_key']);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
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
            Debugger::class,
        ];
    }

    /**
     * Register requested collections within debugger.
     *
     * @param Collection[] $collections
     */
    protected function registerCollections(array $collections)
    {
        $debugger = $this->app->make(Debugger::class);

        foreach ($collections as $collection) {
            $debugger->populateWith($this->app->make($collection));
        }
    }

    /**
     * Set the response key for the debug object
     * (default: debug)
     *
     * @param string key
     */
    protected function setResponseKey($key)
    {
        $debugger = $this->app->make(Debugger::class);

        if($key !== Debugger::DEFAULT_RESPONSE_KEY){
            $debugger->setResponseKey($key);
        }
    }
}
