<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait ServiceTestCaseTrait
{
    /**
     * @param array $options
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    abstract protected function createKernel(array $options = array());

    /**
     * @param string $kernelName
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected static function getSharedKernel($kernelName = 'default')
    {
        if (!isset(KernelRegistry::$kernels[$kernelName])) {
            $kernel = static::createKernel(array());
            $kernel->boot();
            KernelRegistry::$kernels[$kernelName] = $kernel;
        }

        return KernelRegistry::$kernels[$kernelName];
    }

    /**
     * @param $commandName
     * @param string $kernelName
     * @return CommandHelper
     */
    protected static function commandHelper($commandName, $kernelName = 'default')
    {
        $application = new Application($kernel = static::getSharedKernel($kernelName));

        $container = $kernel->getContainer();
        foreach ($application->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        $application->setDispatcher($container->get('event_dispatcher'));
        return new CommandHelper($application->find($commandName));
    }
}