<?php

namespace App\Providers;

use App\Services\WordPress\Capabilities;
use App\Services\WordPress\Setup;
use Rooty\Contracts\Http\Abort as AbortInterface;
use Rooty\Support\ServiceProvider;
use RuntimeException;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Register all WordPress-related services.
     *
     * @return void
     */
    public function register()
    {
        $this->setup();

        $this->registerAbortHandler();
        $this->registerCapabilities();
    }

    /**
     * Boot post-registration services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(Setup::class)->boot();
    }

    /**
     * Register the main WordPress Setup service.
     *
     * @return void
     */
    protected function setup()
    {
        $this->app->singleton(Setup::class, function ($app) {
            return new Setup(
                $app,
                $app->make(AbortInterface::class)
            );
        });
    }

    /**
     * Register the AbortHandler service based on the configured implementation.
     *
     * @throws \RuntimeException If the configured class is invalid.
     * @return void
     */
    protected function registerAbortHandler(): void
    {
        /**
         * Register the AbortHandler service based on the configured class.
         *
         * @return \Rooty\Contracts\Http\Abort
         *
         * @throws \RuntimeException
         */
        $this->app->singleton(AbortInterface::class, function () {
            $class = config('wp.abort.handler');

            $isValid = is_string($class)
                && class_exists($class)
                && is_subclass_of($class, AbortInterface::class);

            if (! $isValid) {
                throw new RuntimeException(
                    "Invalid AbortHandler class configured in [config/wp.php] under ['abort.handler']." . PHP_EOL .
                    "Expected a valid class name implementing " . AbortInterface::class . ', got: ' . var_export($class, true)
                );
            }

            return new $class();
        });

        $this->app->alias(AbortInterface::class, 'wp.abort');
    }

    /**
     * Register the Capabilities service.
     *
     * @return void
     */
    protected function registerCapabilities()
    {
        $this->app->singleton(Capabilities::class);

        $this->app->alias(Capabilities::class, 'wp.caps');
    }
}
