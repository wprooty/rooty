<?php

namespace App\Services\WordPress;

use Rooty\Contracts\Http\Abort;
use Rooty\Foundation\Application;

class Setup
{
    /**
     * Indicates whether the setup has already been booted.
     *
     * @var bool
     */
    protected static $booted = false;

    /**
     * Create a new Setup instance.
     *
     * @param  \Rooty\Foundation\Application  $app
     * @param  \Rooty\Contracts\Http\Abort    $abort      
     */
    public function __construct(
        protected Application $app,
        protected Abort $abort
    )
    {
        $this->setDefaultWpConfig();
    }

    /**
     * Boot the WordPress setup service.
     *
     * Prevents multiple executions by checking a static flag.
     *
     * @return void
     */
    public function boot(): void
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;

        $this->abort->boot();

        add_action('after_setup_theme', fn () => $this->ensurePermalinks());

        add_filter('wp_headers', fn (array $headers) => $this->sendQueuedCookies($headers));
    }

    /**
     * Abort the request with a given HTTP code, message, and title.
     *
     * @param  int|null  $code    HTTP status code (e.g. 403, 404, 500)
     * @param  string  $message  Optional message to display
     * @param  string  $title    Optional title of the error
     * @param  array<string, mixed>  $args   Additional context or metadata
     * @return never
     */
    public function abort(?int $code, string $message = '', string $title = '', array $args = [])
    {
        $this->abort->abort($code, $message, $title, $args);
    }

    /**
     * Define default WordPress-related configuration values
     * if not already set in the application config.
     *
     * @return void
     */
    protected function setDefaultWpConfig(): void
    {
        $defaults = [
            'theme_path' => get_theme_file_path(),
            'theme_url'  => get_theme_file_uri(),
        ];

        foreach ($defaults as $key => $value) {
            if (config("wp.$key") === null) {
                config(["wp.$key" => $value]);
            }
        }
    }

    /**
     * Ensure that the permalink structure is set to `/postname/`.
     *
     * This is enforced automatically at theme setup.
     *
     * @return void
     */
    protected function ensurePermalinks(): void
    {
        $expected = '/%postname%/';

        if (get_option('permalink_structure') !== $expected) {
            update_option('permalink_structure', $expected);
            flush_rewrite_rules();
        }
    }

    /**
     * Send queued cookies from the application's response.
     *
     * Integrates Symfony's cookie management with WordPress headers.
     *
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    protected function sendQueuedCookies(array $headers): array
    {
        if (
            ! $this->app->bound('cookie') ||
            ! $this->app->bound('response')
        ) {
            return $headers;
        }

        $response = $this->app['response'];
        $cookieJar = $this->app['cookie'];

        foreach ($cookieJar->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        foreach ($response->headers->getCookies() as $cookie) {
            header('Set-Cookie: ' . $cookie->__toString(), false);
        }

        return $headers;
    }
}
