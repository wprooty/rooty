<?php

namespace Rooty\Console\Events;

use Rooty\Console\Application;

class ArchStarting
{
    /**
     * Create a new event instance.
     *
     * @param  \Rooty\Console\Application  $arch  The Arch application instance.
     */
    public function __construct(
        public Application $arch,
    ) {
    }
}
