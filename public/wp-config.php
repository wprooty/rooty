<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\BufferingLogger;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;

// Environment loading.
$basePath = dirname(__DIR__);
$envFile = require $basePath . '/bootstrap/environment.php';

if ($envFile !== null) {
    $dotenv = Dotenv::createImmutable($basePath, $envFile);
    $dotenv->safeLoad();
}

// Resolve APP_DEBUG_LOG and set PHP error_log.
$envLog = trim($_ENV['APP_DEBUG_LOG'] ?? '');

if ($envLog !== '') {
    $isAbsolute = str_starts_with($envLog, '/')
        || str_starts_with($envLog, '\\')
        || preg_match('/^[A-Za-z]:[\/\\\\]/', $envLog);

    $resolvedLogPath = $isAbsolute
        ? $envLog
        : $basePath . '/' . ltrim($envLog, '/\\');
} else {
    $resolvedLogPath = $basePath . '/storage/logs/php_error.log';
}

$resolvedLogPath = str_replace('\\', '/', $resolvedLogPath);

ini_set('log_errors', '1');
ini_set('error_log', $resolvedLogPath);

$env = strtolower(trim($_ENV['APP_ENV'] ?? 'production'));
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

if (in_array($env, ['production', 'prod'], true)) {
    $debug = false;
}

$disableFatalHandler = filter_var($_ENV['WP_DISABLE_FATAL_ERROR_HANDLER'] ?? false, FILTER_VALIDATE_BOOLEAN);

define('WP_DEBUG', $debug);
define('WP_DEBUG_DISPLAY', $debug);
define('WP_DEBUG_LOG', $debug ? $resolvedLogPath : false);
define('SCRIPT_DEBUG', $debug);
define('WP_DISABLE_FATAL_ERROR_HANDLER', $disableFatalHandler);

if ($disableFatalHandler) {
    $logger = new BufferingLogger();

    ErrorHandler::register(new ErrorHandler($logger, $debug));

    if (! $debug) {
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
    }
}

// Set the environment type.
define('WP_ENVIRONMENT_TYPE', $_ENV['WP_ENVIRONMENT_TYPE'] ?? 'production');

// Set the default WordPress theme.
define('WP_DEFAULT_THEME', $_ENV['WP_DEFAULT_THEME'] ?? 'rooty');

// Database configuration.
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');
define('DB_COLLATE', $_ENV['DB_COLLATE'] ?? 'utf8mb4_unicode_ci');

// Authentication keys and salts.
define('AUTH_KEY', $_ENV['AUTH_KEY']);
define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY']);
define('LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY']);
define('NONCE_KEY', $_ENV['NONCE_KEY']);
define('AUTH_SALT', $_ENV['AUTH_SALT']);
define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
define('LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT']);
define('NONCE_SALT', $_ENV['NONCE_SALT']);

// Detect HTTPS behind proxy/load balancer.
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// // Set the home URL.
// define('WP_HOME', $_ENV['WP_HOME'] ?? Request::createFromGlobals()->getSchemeAndHttpHost());

// // Set the WordPress directory URL.
// define('WP_SITEURL', $_ENV['WP_SITEURL'] ?? sprintf('%s/%s', WP_HOME, $_ENV['WP_DIR'] ?? 'wordpress'));

// Detect CLI context (Rooty Arch console).
$isCli = defined('ROOTY_CLI') && ROOTY_CLI === true;

// In CLI, prefer APP_URL (from .env) to avoid "http://:/"
$baseUrl = $isCli
    ? ($_ENV['APP_URL'] ?? 'http://localhost')
    : (Request::createFromGlobals()->getSchemeAndHttpHost());

// Set the home URL.
define('WP_HOME', $_ENV['WP_HOME'] ?? $baseUrl);

// Set the WordPress directory URL.
define('WP_SITEURL', $_ENV['WP_SITEURL'] ?? sprintf('%s/%s', WP_HOME, $_ENV['WP_DIR'] ?? 'wordpress'));

// Set the WordPress content directory path.
define('WP_CONTENT_DIR', $_ENV['WP_CONTENT_DIR'] ?? __DIR__);
define('WP_CONTENT_URL', $_ENV['WP_CONTENT_URL'] ?? WP_HOME);

// Determine WP_LANG_DIR.
$langDir = ($_ENV['WP_LANG_DIR'] ?? '') ?: WP_CONTENT_DIR . '/languages';
define('WP_LANG_DIR', $langDir);

// Disable WordPress auto updates.
define('AUTOMATIC_UPDATER_DISABLED', filter_var($_ENV['AUTOMATIC_UPDATER_DISABLED'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Disable WP-Cron.
define('DISABLE_WP_CRON', filter_var($_ENV['DISABLE_WP_CRON'] ?? false, FILTER_VALIDATE_BOOLEAN));

// Prevent file editing from the dashboard.
define('DISALLOW_FILE_EDIT', filter_var($_ENV['DISALLOW_FILE_EDIT'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Disable plugin/theme updates & installation from the dashboard.
define('DISALLOW_FILE_MODS', filter_var($_ENV['DISALLOW_FILE_MODS'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Cleanup WordPress image edits.
define('IMAGE_EDIT_OVERWRITE', filter_var($_ENV['IMAGE_EDIT_OVERWRITE'] ?? true, FILTER_VALIDATE_BOOLEAN));

// Limit the number of post revisions.
define('WP_POST_REVISIONS', (int) ($_ENV['WP_POST_REVISIONS'] ?? 2));

// Absolute path to WordPress.
if (!defined('ABSPATH')) {
    define('ABSPATH', sprintf('%s/%s/', __DIR__, $_ENV['WP_DIR'] ?? 'wordpress'));
}

// Database table prefix.
$table_prefix = $_ENV['DB_TABLE_PREFIX'] ?? 'wp_';

require_once ABSPATH . 'wp-settings.php';
