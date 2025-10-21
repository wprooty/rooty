<?php

namespace Rooty\Console;

use Illuminate\Support\Traits\Macroable;
use LogicException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Command extends SymfonyCommand
{
    use Macroable,
        Concerns\CallsCommands,
        // Concerns\ConfiguresPrompts,
        Concerns\HasParameters,
        // Concerns\InteractsWithIO,
        Concerns\InteractsWithSignals
        // Concerns\PromptsForMissingInput,
    ;

    /**
     * The Rooty application instance.
     *
     * @var \Rooty\Contracts\Foundation\Application
     */
    protected $rooty;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The console command help text.
     *
     * @var string
     */
    protected $help = '';

    /**
     * Indicates whether the command should be shown in the list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Indicates whether only one instance of the command can run at any given time.
     *
     * @var bool
     */
    protected $isolated = false;

    /**
     * The default exit code for isolated commands.
     *
     * @var int
     */
    protected $isolatedExitCode = self::SUCCESS;

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Create a new console command instance.
     */
    public function __construct()
    {
        if (isset($this->signature)) {
            $this->configureUsingFluentDefinition();
        } else {
            parent::__construct($this->name);
        }

        if (! empty($this->description)) {
            $this->setDescription($this->description);
        }

        if (! empty($this->help)) {
            $this->setHelp($this->help);
        }

        $this->setHidden($this->isHidden());

        if (isset($this->aliases)) {
            $this->setAliases((array) $this->aliases);
        }

        if (! isset($this->signature)) {
            $this->specifyParameters();
        }

        // if ($this instanceof Isolatable) {
        //     $this->configureIsolation();
        // }
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition()
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);

        parent::__construct($this->name = $name);

        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * Configure the console command for isolation.
     *
     * @return void
     */
    protected function configureIsolation()
    {
        // TODO
    }

    /**
     * Run the console command.
     */
    #[\Override]
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        // if (method_exists($this, 'configurePrompts')) {
        //     $this->configurePrompts($input);
        // }

        try {
            return parent::run($input, $output);
        } finally {
            if (method_exists($this, 'untrap')) {
                $this->untrap();
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // // if ($this instanceof Isolatable && $this->option('isolated') !== false &&
        // //     ! $this->commandIsolationMutex()->create($this)) {
        // //     $this->comment(sprintf(
        // //         'The [%s] command is already running.', $this->getName()
        // //     ));

        // //     return (int) (is_numeric($this->option('isolated'))
        // //         ? $this->option('isolated')
        // //         : $this->isolatedExitCode);
        // // }

        // $method = method_exists($this, 'handle') ? 'handle' : '__invoke';

        // try {
        //     return (int) $this->rooty->call([$this, $method]);
        // } catch (ManuallyFailedException $e) {
        //     // $this->components->error($e->getMessage());

        //     return static::FAILURE;
        // } finally {
        //     // if ($this instanceof Isolatable && $this->option('isolated') !== false) {
        //     //     $this->commandIsolationMutex()->forget($this);
        //     // }
        // }

        $method = method_exists($this, 'handle') ? 'handle' : '__invoke';

        if (! method_exists($this, $method)) {
            throw new LogicException(sprintf(
                'Command [%s] must have either a handle() or __invoke() method.',
                static::class
            ));
        }

        try {
            return (int) $this->rooty->call([$this, $method]);
        } catch (ManuallyFailedException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return static::FAILURE;
        }
    }

    /**
     * Get a command isolation mutex instance for the command.
     */
    protected function commandIsolationMutex()
    {
        // TODO
    }

    /**
     * Resolve the console command instance for the given command.
     *
     * @param  \Symfony\Component\Console\Command\Command|string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function resolveCommand($command)
    {
        if (is_string($command)) {
            if (! class_exists($command)) {
                return $this->getApplication()->find($command);
            }

            $command = $this->rooty->make($command);
        }

        if ($command instanceof SymfonyCommand) {
            $command->setApplication($this->getApplication());
        }

        if ($command instanceof self) {
            $command->setRooty($this->getRooty());
        }

        return $command;
    }

    /**
     * Fail the command manually.
     *
     * @param  \Throwable|string|null  $exception
     * @return never
     *
     * @throws \Rooty\Console\ManuallyFailedException|\Throwable
     */
    public function fail(Throwable|string|null $exception = null)
    {
        if (is_null($exception)) {
            $exception = 'Command failed manually.';
        }

        if (is_string($exception)) {
            $exception = new ManuallyFailedException($exception);
        }

        throw $exception;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    #[\Override]
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function setHidden(bool $hidden = true): static
    {
        parent::setHidden($this->hidden = $hidden);

        return $this;
    }

    /**
     * Get the Rooty application instance.
     *
     * @return \Rooty\Contracts\Foundation\Application
     */
    public function getRooty()
    {
        return $this->rooty;
    }

    /**
     * Set the Rooty application instance.
     *
     * @param  \Rooty\Contracts\Container\Container  $rooty
     * @return void
     */
    public function setRooty($rooty)
    {
        $this->rooty = $rooty;
    }
}
