<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

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
}