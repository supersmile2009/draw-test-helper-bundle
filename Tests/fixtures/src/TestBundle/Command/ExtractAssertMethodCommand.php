<?php

namespace TestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractAssertMethodCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test:extract_assert_method')
            ->setDescription(
                'Output to the screen all the assert method of the PHPUnit_Framework_Assert class.' .
                'Use for the PropertyHelper documentation.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reflectionClass = new \ReflectionClass('PHPUnit_Framework_Assert');

        foreach ($reflectionClass->getMethods() as $method) {
            if (strpos($method->getName(), 'assert') !== 0) {
                continue;
            }

            $parameters = array();
            foreach ($method->getParameters() as $parameter) {
                $parameterDefinition = '$' . $parameter->getName();
                if ($parameter->isDefaultValueAvailable()) {
                    $parameterDefinition .= ' = ' . var_export($parameter->getDefaultValue(), 1);
                }

                $parameters[] = $parameterDefinition;
            }

            $output->writeln(' * @method $this ' . $method->getName() . '(' . implode(', ', $parameters) . ')');
        }
    }
}