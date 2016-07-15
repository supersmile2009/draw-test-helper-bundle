<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CommandHelper extends CommandTester
{
    /**
     * @var Command
     */
    private $theCommand;

    /**
     * Constructor.
     *
     * @param Command $command A Command instance to test.
     */
    public function __construct(Command $command)
    {
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
}