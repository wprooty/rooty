<?php

namespace Rooty\Support;

use Symfony\Component\Process\PhpExecutableFinder;

if (! function_exists('Rooty\Support\php_binary')) {
    /**
     * Determine the PHP Binary.
     */
    function php_binary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }
}

if (! function_exists('Rooty\Support\arch_binary')) {
    /**
     * Determine the proper Arch executable.
     */
    function arch_binary(): string
    {
        return defined('ARCH_BINARY') ? ARCH_BINARY : 'arch';
    }
}
