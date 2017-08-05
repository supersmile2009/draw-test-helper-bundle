<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;


trait NonRebootableKernelTestTrait
{

    /**
     * Overriding Symfony's default teatDown method, which reboots kernel after each test, which is very slow.
     *
     * Kernel or client reboots should be added manually to the tests that really need it.
     */
    protected function tearDown()
    {
        // This method is doing nothing intentionally. If you need to add some code here, feel free to do so.
    }
}