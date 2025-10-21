<?php

/**
 * Central environment file selector for both WordPress and Rooty.
 *
 * This file must return the name of the .env file to load.
 * If the chosen file does not exist, it will fallback to the default `.env`.
 */

$basePath = dirname(__DIR__);

// Explicit choice: change this to select a specific env file
$envFile = '.env';

// If chosen file does not exist, fallback to default `.env`
if (! is_file($basePath . '/' . $envFile)) {
    if (is_file($basePath . '/.env')) {
        $envFile = '.env';
    } else {
        // Nothing found: return null (Dotenv will simply load nothing)
        return null;
    }
}

return $envFile;
