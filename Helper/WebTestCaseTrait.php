<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Client;

trait WebTestCaseTrait
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * Some methods in test helpers can create their own clients if static::$client is not set.
     * We don't really want that. We want all tests to re-use already created client.
     *
     * If test doesn't have its own setUpBeforeClass method, this one will be used, to avoid creating new clients
     *
     * If test has setUpBeforeClass, you should call static::setUpClient() in it, to ensure using existing client.
     */
    public static function setUpBeforeClass()
    {
        static::setUpClient();
    }

    /**
     * Creates new client and stores it in static variable and in Globals.
     * Provides the same client on each call allowing to reuse it, unless you set $reload to true to force reload new client
     *
     * Client's kernel reboot is disabled by default, to speed up tests.
     *
     * @param bool $reload - Should we reload new client?
     * @param bool $disableReboot - Disable client's kernel reboot before each request
     *
     * @return Client
     */
    public static function setUpClient($reload = false, $disableReboot = true)
    {
        if (!isset($GLOBALS['client']) || $GLOBALS['client'] === null || $reload) {
            static::$client = static::createClient($options = [], $server = []);
            if ($disableReboot === true) {
                static::$client->disableReboot();
            }
            $GLOBALS['client'] = static::$client;
        } else {
            static::$client = $GLOBALS['client'];
        }

        return static::$client;
    }

    /**
     * For those tests that don't define their own tearDownAfterClass method, this method will be used.
     * Those tests, that override this method with their own, should call baseTearDownAfterClass after their own logic.
     */
    public static function tearDownAfterClass()
    {
        static::baseTearDownAfterClass();
    }

    /**
     * Since PHP is really busy with running tests, sometimes it won't run GC if we don't tell it to do so.
     */
    public static function baseTearDownAfterClass()
    {
        gc_enable();
        gc_collect_cycles();
    }

    /**
     * Overriding Symfony's default teatDown method, which reboots kernel after each test, which is very slow.
     *
     * Kernel or client reboots should be added manually to the tests that really need it.
     */
    protected function tearDown()
    {
        // This method is doing nothing intentionally. If you need to add some code here, feel free to do so.
    }

    protected static function clearClientEntityManagerCache($client = null)
    {
        if ($client === null) {
            $client = static::$client;
        }
        if ($client !== null) {
            if ($client->getKernel()->getContainer() !== null) {
                $client->getKernel()->getContainer()->get('doctrine')->getManager()->clear();
            }
        }
    }

    /**
     * @return RequestHelper
     */
    public function requestHelper(Client $client = null)
    {
        if (is_null($client)) {
            $client = static::$client;
        }

        return RequestHelper::factory($this, $client);
    }
}