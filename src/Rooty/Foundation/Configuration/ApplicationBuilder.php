<?php

namespace Rooty\Foundation\Configuration;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Rooty\Contracts\Console\Kernel as ConsoleKernel;
use Rooty\Foundation\Application;
use Rooty\Foundation\Bootstrap\RegisterProviders;
use Rooty\Foundation\Support\Providers\EventServiceProvider as AppEventServiceProvider;

class ApplicationBuilder
{
    /**
     * The service provider that are marked for registration.
     *
     * @var array
     */
    protected array $pendingProviders = [];

    /**
     * Create a new application builder instance.
     */
    public function __construct(protected Application $app)
    {
    }

    /**
     * Set the environment file to be loaded by the underlying Application.
     */
    public function loadEnvironmentFrom(string $file): static
    {
        $this->app->loadEnvironmentFrom($file);

        return $this;
    }

    /**
     * Register the standard kernel classes for the application.
     *
     * @return $this
     */
    public function withKernels()
    {
        $this->app->singleton(
            \Rooty\Contracts\Http\Kernel::class,
            \Rooty\Foundation\Http\Kernel::class
        );

        $this->app->singleton(
            \Rooty\Contracts\Console\Kernel::class,
            \Rooty\Foundation\Console\Kernel::class,
        );

        return $this;
    }

    /**
     * Register additional Arch commands with the application.
     *
     * @param  array  $commands
     * @return $this
     */
    public function withCommands(array $commands = [])
    {
        if (empty($commands)) {
            $commands = [$this->app->path('Console/Commands')];
        }

        $this->app->afterResolving(ConsoleKernel::class, function ($kernel) use ($commands) {
            [$commands, $paths] = (new Collection($commands))->partition(fn ($command) => class_exists($command));
            [$routes, $paths] = $paths->partition(fn ($path) => is_file($path));

            $this->app->booted(static function () use ($kernel, $commands, $paths, $routes) {
                $kernel->addCommands($commands->all());
                $kernel->addCommandPaths($paths->all());
                // $kernel->addCommandRoutePaths($routes->all());
            });
        });

        return $this;
    }

    /**
     * Register the facades for the application.
     *
     * @param  bool  $aliases
     * @param  array  $userAliases
     * @return $this
     */
    public function withFacades(bool $aliases = true, array $userAliases = [])
    {
        Facade::setFacadeApplication($this->app);

        if ($aliases) {
            if (! Application::facadeAliasesRegistered()) {
                Application::markFacadeAliasesRegistered();

                $merged = array_merge(Application::getDefaultFacadeAliases(), $userAliases);

                foreach ($merged as $original => $alias) {
                    if (! class_exists($alias)) {
                        class_alias($original, $alias);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Register additional service providers.
     *
     * @param  array  $providers
     * @param  bool  $withBootstrapProviders
     * @return $this
     */
    public function withProviders(array $providers = [], bool $withBootstrapProviders = true)
    {
        RegisterProviders::merge(
            $providers,
            $withBootstrapProviders
                ? $this->app->getBootstrapProvidersPath()
                : null
        );

        return $this;
    }

    /**
     * Register the core event service provider for the application.
     *
     * @param  array|bool  $discover
     * @return $this
     */
    public function withEvents(array|bool $discover = [])
    {
        if (is_array($discover) && count($discover) > 0) {
            AppEventServiceProvider::setEventDiscoveryPaths($discover);
        }

        if ($discover === false) {
            AppEventServiceProvider::disableEventDiscovery();
        }

        if (! isset($this->pendingProviders[AppEventServiceProvider::class])) {
            $this->app->booting(function () {
                $this->app->register(AppEventServiceProvider::class);
            });
        }

        $this->pendingProviders[AppEventServiceProvider::class] = true;

        return $this;
    }

    /**
     * Register a callback to be invoked when the application's service providers are registered.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function registered(callable $callback)
    {
        $this->app->registered($callback);

        return $this;
    }

    /**
     * Register a callback to be invoked when the application is "booting".
     *
     * @param  callable  $callback
     * @return $this
     */
    public function booting(callable $callback)
    {
        $this->app->booting($callback);

        return $this;
    }

    /**
     * Register a callback to be invoked when the application is "booted".
     *
     * @param  callable  $callback
     * @return $this
     */
    public function booted(callable $callback)
    {
        $this->app->booted($callback);

        return $this;
    }

    /**
     * Get the application instance.
     *
     * @return \Rooty\Foundation\Application
     */
    public function create()
    {
        return $this->app;
    }
}
