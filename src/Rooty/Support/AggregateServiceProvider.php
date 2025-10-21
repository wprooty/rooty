<?php

namespace Rooty\Support;

abstract class AggregateServiceProvider extends ServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<int, class-string<\Rooty\Support\ServiceProvider>>
     */
    protected $providers = [];

    /**
     * The service provider instances.
     *
     * @var array<int, \Rooty\Support\ServiceProvider>
     */
    protected $instances = [];

    /**
     * Register the service providers.
     *
     * @return void
     */
    public function register()
    {
        $this->instances = [];

        foreach ($this->providers as $provider) {
            $this->instances[] = $this->app->register($provider);
        }
    }

    /**
     * Get the services provided by all of the providers.
     *
     * @return array<int, string>
     */
    public function provides()
    {
        $provides = [];

        foreach ($this->providers as $provider) {
            $instance = $this->app->resolveProvider($provider);

            $provides = array_merge($provides, $instance->provides());
        }

        return $provides;
    }
}
