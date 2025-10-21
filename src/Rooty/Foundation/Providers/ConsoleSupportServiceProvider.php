<?php

namespace Rooty\Foundation\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Rooty\Support\AggregateServiceProvider;

/**
 * @method array provides()
 */
class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        ArchServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
