<?php

namespace Rooty\Contracts\Console;

interface Kernel
{
    /**
     * Bootstrap the application for console commands.
     *
     * @return void
     */
    public function bootstrap();

    /**
     * Handle an incoming console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface|null  $output
     * @return int
     */
    public function handle($input, $output = null);

    /**
     * Run a console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface|null  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null);

    // /**
    //  * Queue a console command by name.
    //  *
    //  * @param  string  $command
    //  * @param  array  $parameters
    //  * @return \Illuminate\Foundation\Bus\PendingDispatch
    //  */
    // public function queue($command, array $parameters = []);

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all();

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output();

    /**
     * Terminate the application.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  int  $status
     * @return void
     */
    public function terminate($input, $status);
}
