<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\WebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebTestCaseTraitTest extends WebTestCase
{
    use WebTestCaseTrait;

    public function testSetUpBeforeClass()
    {
        //Must be call manually, if not it will be ignore by test coverage
        static::setUpBeforeClass();
        $this->assertNotNull(static::$client);
        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Client', static::$client);
    }

    public function testRequestHelperWithoutClient()
    {
        $requestHelper = $this->requestHelper();
        $this->assertInstanceOf('Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper', $requestHelper);

        $this->assertSame(static::$client, $requestHelper->getClient());
        $this->assertSame($this, $requestHelper->getTestCase());
    }

    public function testRequestHelperWithClient()
    {
        $requestHelper = $this->requestHelper($client = static::createClient());
        $this->assertInstanceOf('Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper', $requestHelper);

        $this->assertNotSame(static::$client, $requestHelper->getClient());
        $this->assertSame($client, $requestHelper->getClient());
        $this->assertSame($this, $requestHelper->getTestCase());
    }
}