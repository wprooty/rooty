<?php

namespace Rooty\Foundation;

use Symfony\Component\HttpFoundation\Request;

class ThemeKernel
{
    public static function boot(): void
    {
        $app = app();

        if (! $app->isBooted()) {
            $app->bootstrapWith([
                \Rooty\Foundation\Bootstrap\LoadEnvironmentVariables::class,
                \Rooty\Foundation\Bootstrap\LoadConfiguration::class,
                \Rooty\Foundation\Bootstrap\RegisterProviders::class,
                \Rooty\Foundation\Bootstrap\BootProviders::class,
            ]);

            $app->boot();
        }

        $app->handleRequest(Request::createFromGlobals());
    }
}
