<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait ServiceTestCaseTrait
{
    /**
     * @var bool
     */
    protected static $firstBoot = true;

    protected static function clearKernelEntityManagerCache()
    {
        if (static::getSharedKernel()->getContainer() !== null) {
            static::getSharedKernel()->getContainer()->get('doctrine')->getManager()->clear();
        }
    }

    /**
     * @param string $kernelName
     * @param boolean $debug - should we create a kernel with debug? Normally additional kernels don't need debug,
     * @param boolean $checkConnection
     * it only slows the test down
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     *
     * @throws \InvalidArgumentException
     */
    protected static function getSharedKernel($kernelName = 'default', $debug = false, $checkConnection = true)
    {
        if (!isset(KernelRegistry::$kernels[$kernelName])) {
            if (static::$firstBoot || $kernelName === 'default') {
                $debug = true;
            }
            if ($kernelName === 'delete') {
                $kernel = static::getKernelFromGlobals($kernelName);
                if ($kernel === null) {
                    $kernel = static::createKernel(['debug' => false]);
                    $GLOBALS['kernels']['delete'] = $kernel;
                    $kernel->boot();
                }
            } else {
                $kernel = static::createKernel(['debug' => $debug]);
                $kernel->boot();
            }

            KernelRegistry::$kernels[$kernelName] = $kernel;
        }

        static::$firstBoot = false;

        //make sure when we get a kernel its always booted first.
        KernelRegistry::$kernels[$kernelName]->boot();

        if ($checkConnection) {
            $doctrine = KernelRegistry::$kernels[$kernelName]->getContainer()->get('doctrine');

            /** @var EntityManager $manager */
            $manager = $doctrine->getManager();
            if ($manager->isOpen() === false) {
                $doctrine->resetManager();
            }
        }

        return KernelRegistry::$kernels[$kernelName];
    }

    private static function getKernelFromGlobals($kernelName)
    {
        if (isset($GLOBALS['kernels'][$kernelName])) {
            return $GLOBALS['kernels'][$kernelName];
        }

        return null;
    }

    /**
     * @param $commandName
     * @param string $kernelName
     *
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

        return new CommandHelper($container, $application->find($commandName));
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    abstract protected function createKernel(array $options = []);

    /**
     * @param $object
     * @param $propertyName
     * @param $value
     *
     * @return mixed
     */
    protected function replacePropertyValue($object, $propertyName, $value)
    {
        $objectReflection = new \ReflectionObject($object);
        $propertyReflection = $objectReflection->getProperty($propertyName);
        if ($propertyReflection->isProtected() || $propertyReflection->isPrivate()) {
            $propertyReflection->setAccessible(true);
        }

        $oldValue = $propertyReflection->getValue($object);
        $propertyReflection->setValue($object, $value);

        return $oldValue;
    }

    /**
     * @param $object
     * @param $propertyName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function replacePropertyWithAutoGeneratedMock($object, $propertyName)
    {
        $objectReflection = new \ReflectionObject($object);
        $propertyReflection = $objectReflection->getProperty($propertyName);
        if ($propertyReflection->isProtected() || $propertyReflection->isPrivate()) {
            $propertyReflection->setAccessible(true);
        }

        $oldValue = $propertyReflection->getValue($object);
        $value = $this
            ->getMockBuilder(get_class($oldValue))
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $propertyReflection->setValue($object, $value);

        return $value;
    }

}
