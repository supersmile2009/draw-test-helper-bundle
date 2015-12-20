<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestHelperTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private static $client;

    public static function setUpBeforeClass()
    {
        static::$client = static::createClient();
    }

    public function testFactory()
    {
        $requestHelper = RequestHelper::factory($this, static::$client);
        $this->assertInstanceOf('Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper', $requestHelper);

        return $requestHelper;
    }

    /**
     * @depends testFactory
     *
     * @param RequestHelper $requestHelper
     * @return RequestHelper
     */
    public function testGetEventDispatcher(RequestHelper $requestHelper)
    {
        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcherInterface',
            $requestHelper->getEventDispatcher()
        );

        return $requestHelper;
    }

    /**
     * @depends testFactory
     */
    public function testGetClient()
    {
        $requestHelper = RequestHelper::factory($this, static::$client);
        $this->assertSame(static::$client, $requestHelper->getClient());

        return $requestHelper;
    }

    /**
     * @depends testFactory
     */
    public function testGetTestCase()
    {
        $requestHelper = RequestHelper::factory($this, static::$client);
        $this->assertSame($this, $requestHelper->getTestCase());

        return $requestHelper;
    }

    /**
     * @depends testFactory
     *
     * @param RequestHelper $requestHelper
     */
    public function testUriMutator(RequestHelper $requestHelper)
    {
        $this->assertNull($requestHelper->getUri());

        $requestHelper->setUri("/test");
        $this->assertSame("/test", $requestHelper->getUri());

        $requestHelper->setUri(null);
        $this->assertNull($requestHelper->getUri());

        return $requestHelper;
    }

    /**
     * @depends testFactory
     *
     * @param RequestHelper $requestHelper
     */
    public function testMethodMutator(RequestHelper $requestHelper)
    {
        $this->assertNull($requestHelper->getMethod());

        $requestHelper->setMethod("GET");
        $this->assertSame("GET", $requestHelper->getMethod());

        $requestHelper->setMethod(null);
        $this->assertNull($requestHelper->getMethod());

        return $requestHelper;
    }

    public function provideTestFluentHttpMethodUri()
    {
        return array(
            array('head', 'HEAD', '/head-uri'),
            array('get', 'GET', '/get-uri'),
            array('post', 'POST', '/post-uri'),
            array('put', 'PUT', '/put-uri'),
            array('patch', 'PATCH', '/patch-uri'),
            array('delete', 'DELETE', '/delete-uri'),
            array('purge', 'PURGE', '/purge-uri'),
            array('options', 'OPTIONS', '/options-uri'),
            array('trace', 'TRACE', '/trace-uri'),
            array('connect', 'CONNECT', '/connect-uri'),
        );
    }

    /**
     * @dataProvider provideTestFluentHttpMethodUri
     * @depends      testFactory
     */
    public function testProvideTestFluentHttpMethodUri($methodName, $httpMethod, $uri, RequestHelper $requestHelper)
    {
        $reflectionMethod = new \ReflectionMethod(get_class($requestHelper), $methodName);

        //This is to test the fluent interface
        $this->assertSame($requestHelper, $reflectionMethod->invoke($requestHelper, $uri));

        $this->assertSame($httpMethod, $requestHelper->getMethod());
        $this->assertSame($uri, $requestHelper->getUri());
    }

    /**
     * @depends testFactory
     * @depends testMethodMutator
     * @depends testUriMutator
     *
     * @param RequestHelper $requestHelper
     */
    public function testJsonHelper()
    {
        $this->assertInstanceOf(
            'Draw\Bundle\DrawTestHelperBundle\Helper\JsonHelper',
            RequestHelper::factory($this, static::$client)->jsonHelper()
        );

    }

    public function testPropertyHelper()
    {
        $this->assertInstanceOf(
            'Draw\Bundle\DrawTestHelperBundle\Helper\PropertyHelper',
            RequestHelper::factory($this, static::$client)->propertyHelper('test')
        );
    }

    public function testLogHelper()
    {
        $this->assertInstanceOf(
            'Draw\Bundle\DrawTestHelperBundle\Helper\LogHelper',
            RequestHelper::factory($this, static::$client)->logHelper()
        );
    }

    public function testSqlHelper()
    {
        $this->assertInstanceOf(
            'Draw\Bundle\DrawTestHelperBundle\Helper\SqlHelper',
            RequestHelper::factory($this, static::$client)->sqlHelper()
        );
    }

    public function testExpectingStatusCode()
    {
        RequestHelper::factory($this, static::$client)
            ->get("/test")
            ->expectingStatusCode(200)
            ->execute();
    }

    public function testExpectingStatusCodeFailed()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            "Response raw content:\n{\"key\":\"value\"}\nFailed asserting that 200 is identical to 204."
        );

        RequestHelper::factory($this, static::$client)
            ->get("/test")
            ->expectingStatusCode(204)
            ->execute();
    }

    /**
     * @depends testExpectingStatusCode
     */
    public function testExpectingNoContent()
    {
        RequestHelper::factory($this, static::$client)
            ->get("/no-content")
            ->expectingStatusCode(204)
            ->expectingNoContent()
            ->execute();
    }

    public function testExpectingNoContentWithTypeFailed()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            "Failed asserting that 'text/html; charset=UTF-8' is null."
        );

        RequestHelper::factory($this, static::$client)
            ->get("/with-content")
            ->expectingNoContent()
            ->execute();
    }

    public function testExpectingNoContentNotTypeFailed()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            "Response raw content:\ncontent\nFailed asserting that a string is empty."
        );

        RequestHelper::factory($this, static::$client)
            ->get("/with-content-no-type")
            ->expectingNoContent()
            ->execute();
    }
}