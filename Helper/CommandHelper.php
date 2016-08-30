<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandHelper extends CommandTester
{
    /**
     * @var Command
     */
    private $theCommand;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param Command $command A Command instance to test.
     */
    public function __construct(ContainerInterface $container, Command $command)
    {
        $this->container = $container;
        $this->theCommand = $command;
        parent::__construct($command);
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->theCommand;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}