<?php

namespace Rooty\Foundation\Http;

use Carbon\CarbonInterval;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\InteractsWithTime;
use Rooty\Contracts\Foundation\Application;
use Rooty\Contracts\Http\Kernel as KernelContract;
use Rooty\Foundation\Events\Terminating;
use Rooty\Foundation\Http\Events\RequestHandled;
use Rooty\Http\Dispatcher;
use Rooty\Http\Response;
use Throwable;

class Kernel implements KernelContract
{
    use InteractsWithTime;

    /**
     * The application instance.
     */
    protected Application $app;

    /**
     * The bootstrap classes for the application.
     */
    protected array $bootstrappers = [
        \Rooty\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Rooty\Foundation\Bootstrap\LoadConfiguration::class,
        \Rooty\Foundation\Bootstrap\RegisterProviders::class,
        \Rooty\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * All of the registered request duration handlers.
     */
    protected array $requestLifecycleDurationHandlers = [];

    /**
     * When the kernel starting handling the current request.
     */
    protected ?Carbon $requestStartedAt = null;

    /**
     * Create a new HTTP kernel instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Bootstrap the application for handling HTTP requests.
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the bootstrap classes for the application.
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Handle an incoming HTTP request.
     */
    public function handle($request)
    {
        $this->requestStartedAt = Carbon::now();

        try {
            $response = $this->sendRequestThroughRouter($request);

            $this->app['events']->dispatch(new RequestHandled($request, $response));

            return $response;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Send the given request through the application dispatcher.
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        $this->bootstrap();

        $dispatcher = $this->app->make(Dispatcher::class);

        return $dispatcher($request);
    }

    /**
     * Terminate the request lifecycle.
     */
    public function terminate($request, $response)
    {
        $this->app['events']->dispatch(new Terminating);
        $this->app->terminate();

        if ($this->requestStartedAt === null) {
            return;
        }

        $this->requestStartedAt->setTimezone(
            $this->app['config']->get('app.timezone') ?? 'UTC'
        );

        $end = Carbon::now();
        foreach ($this->requestLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
            if ($this->requestStartedAt->diffInMilliseconds($end) > $threshold) {
                $handler($this->requestStartedAt, $request, $response);
            }
        }

        $this->requestStartedAt = null;
    }

    /**
     * Register a callback for long request durations.
     */
    public function whenRequestLifecycleIsLongerThan($threshold, callable $handler)
    {
        $threshold = $threshold instanceof DateTimeInterface
            ? $this->secondsUntil($threshold) * 1000
            : $threshold;

        $threshold = $threshold instanceof CarbonInterval
            ? $threshold->totalMilliseconds
            : $threshold;

        $this->requestLifecycleDurationHandlers[] = [
            'threshold' => $threshold,
            'handler' => $handler,
        ];
    }

    /**
     * Get when the request started.
     */
    public function requestStartedAt()
    {
        return $this->requestStartedAt;
    }

    /**
     * Get the application instance.
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the application instance.
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }
}
