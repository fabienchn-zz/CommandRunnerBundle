<?php

namespace FabulousCo\CommandRunnerBundle\Component;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Fabien Cohen <fabiencohen96@gmail.com>
 *
 * Class CommandRunner
 * @package Component\CommandRunner
 */
class CommandRunner
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var
     */
    private $command;

    /**
     * @var array
     */
    private $arguments;

    /**
     * CommandRunner constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);

        $this->arguments = [];
    }

    /**
     * @param KernelInterface $kernel
     * @return self
     */
    public static function build(KernelInterface $kernel): self
    {
        return new self($kernel);
    }

    /**
     * @param array $argument
     * @return self
     */
    public function addArgument(array $argument): self
    {
        $this->arguments = array_merge($this->arguments, $argument);

        return $this;
    }

    /**
     * @param string $command
     * @return self
     */
    public function command(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param string $env
     * @return self
     */
    public function env(string $env): self
    {
        $this->addArgument(['--env' => $env]);

        return $this;
    }

    /**
     * @return self
     */
    public function force(): self
    {
        $this->addArgument(['--force' => 'true']);

        return $this;
    }

    /**
     * @param array $additionalArguments
     * @return string
     */
    public function run(array $additionalArguments = []): string
    {
        if (count($additionalArguments)) {
            $this->addArgument($additionalArguments);
        }

        $output = $this->runCommand();

        // clean out
        $this->arguments = [];

        return $output;
    }

    /**
     * @return string
     */
    private function runCommand(): string
    {
        $this->application->setAutoExit(false);

        $input = new ArrayInput(array_merge(
            ['command' => $this->command],
            $this->arguments
        ));

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();

        $this->application->run($input, $output);

        return $output->fetch();
    }
}
