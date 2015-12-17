<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestHelperTest extends WebTestCase
{
    public function testA()
    {
        $client = static::createClient();
        $requestHelper = new RequestHelper($this, $client);

        $requestHelper
            ->get("/test")
            ->asJson()
            ->propertyHelper('key')->isSameAs('value')->attach()
            ->execute();
    }
}