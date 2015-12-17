<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Client;

trait WebTestCaseTrait
{
    /**
     * @var Client
     */
    protected static $client;

    public static function setUpBeforeClass()
    {
        static::$client = static::createClient();
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