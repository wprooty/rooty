<?php

use Rooty\Foundation\Application;

/**
 * Bootstrap the Rooty application.
 *
 * This script is executed before handling any request.
 * It configures the Application and loads the environment file
 * shared with WordPress (see bootstrap/environment.php).
 */

// // Resolve which .env file to load (shared with WordPress).
// $envFile = require __DIR__ . '/environment.php';

// Configure and return the Application instance.
return Application::configure(basePath: dirname(__DIR__))
    // ->loadEnvironmentFrom($envFile)
    ->create();
