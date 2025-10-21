<?php

namespace Rooty\Contracts\Http;

interface Kernel
{
    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap();

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Rooty\Http\Response
     */
    public function handle($request);

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Rooty\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response);

    /**
     * Get the Rooty application instance.
     *
     * @return \Rooty\Contracts\Foundation\Application
     */
    public function getApplication();
}
