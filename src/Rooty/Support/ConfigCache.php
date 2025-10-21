<?php

namespace Rooty\Support;

use Illuminate\Filesystem\Filesystem;
use LogicException;
use Rooty\Contracts\Foundation\Application;
use Throwable;

class ConfigCache
{
    /**
     * Build the config cache file (equivalent of > arch config:cache).
     *
     * @param  \Rooty\Foundation\Application  $app
     * @return void
     *
     * @throws \LogicException
     */
    public static function build(Application $app)
    {
        $files = new Filesystem();

        $configPath = $app->getCachedConfigPath();

        // Clear any existing config cache
        if ($files->exists($configPath)) {
            $files->delete($configPath);
        }

        // Load a fresh application instance
        $freshApp = require $app->bootstrapPath('app.php');
        $freshApp->useStoragePath($app->storagePath());
        $freshApp->bootstrapWith([
            \Rooty\Foundation\Bootstrap\LoadConfiguration::class,
        ]);

        $config = $freshApp['config']->all();

        // Write the cached config file
        $files->put(
            $configPath,
            '<?php return ' . var_export($config, true) . ';' . PHP_EOL
        );

        // Sanity check
        try {
            require $configPath;
        } catch (Throwable $e) {
            $files->delete($configPath);
            throw new LogicException('Your configuration files are not serializable.', 0, $e);
        }
    }
}
