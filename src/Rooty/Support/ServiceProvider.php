<?php

namespace Rooty\Support;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Rooty\Console\Application as Arch;

abstract class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register the package's custom Arch commands.
     *
     * @param  mixed  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Arch::starting(function ($arch) use ($commands) {
            $arch->resolveCommands($commands);
        });
    }
}
