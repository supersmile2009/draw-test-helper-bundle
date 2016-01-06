<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Bundle\FrameworkBundle\Client;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RequestHelper
{
    const EVENT_NEW_HELPER = "requestHelper.newHelper";

    const EVENT_PRE_REQUEST = "requestHelper.preRequest";

    const EVENT_POST_REQUEST = "requestHelper.postRequest";

    /**
     * @var PHPUnit_Framework_TestCase
     */
    private $testCase;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string|null
     */
    private $uri;

    /**
     * @var string|null
     */
    private $method;

    public $assertions = [
        "statusCode" => null,
        "responseContentType" => null,
        "against" => null,
    ];

    private $servers = [];

    public function __construct(PHPUnit_Framework_TestCase $testCase, Client $client)
    {
        $this->client = $client;
        $this->testCase = $testCase;
        $this->eventDispatcher = new EventDispatcher();
        $this->expectingStatusCode(200);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return PHPUnit_Framework_TestCase
     */
    public function getTestCase()
    {
        return $this->testCase;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Set the HTTP method to HEAD and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function head($uri = null)
    {
        $this->setMethod('HEAD');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to GET and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function get($uri = null)
    {
        $this->setMethod('GET');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to POST and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function post($uri = null)
    {
        $this->setMethod('POST');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to PUT and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function put($uri = null)
    {
        $this->setMethod('PUT');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to PATCH and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function patch($uri = null)
    {
        $this->setMethod('PATCH');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to DELETE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function delete($uri = null)
    {
        $this->setMethod('DELETE');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to PURGE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function purge($uri = null)
    {
        $this->setMethod('PURGE');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to OPTIONS and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function options($uri = null)
    {
        $this->setMethod('OPTIONS');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to TRACE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function trace($uri = null)
    {
        $this->setMethod('TRACE');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Set the HTTP method to CONNECT and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @return $this
     */
    public function connect($uri = null)
    {
        $this->setMethod('CONNECT');
        $this->setUri($uri);

        return $this;
    }

    /**
     * Return the internal event dispatcher.
     *
     * @see RequestHelper::addListener
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * A a listener to the internal event dispatcher.
     *
     * Return $this for a fluent interface.
     *
     * @param $eventName
     * @param $listener
     * @param int $priority
     * @return $this
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    /**
     * @return LogHelper
     */
    public function logHelper()
    {
        return LogHelper::instantiate($this);
    }

    /**
     * @param null|integer $maximumQueryCount The maximum query count to set of not null
     *
     * @return SqlHelper
     */
    public function sqlHelper($maximumQueryCount = null)
    {
        $sqlHelper = SqlHelper::instantiate($this);

        if (!is_null($maximumQueryCount)) {
            $sqlHelper->setMaximumQueryCount($maximumQueryCount);
        }

        return $sqlHelper;
    }

    /**
     * @return JsonHelper
     */
    public function jsonHelper()
    {
        return JsonHelper::instantiate($this);
    }

    public function expectContentType($contentType)
    {
        $this->assertions["responseContentType"] = function () use ($contentType) {
            $response = $this->client->getResponse();
            $this->testCase->assertTrue(
                $response->headers->contains('Content-Type', $contentType),
                $response->headers
            );
        };

        return $this;
    }

    public function expectingNoContent()
    {
        $this->assertions["responseContentType"] = function () {
            $response = $this->client->getResponse();
            $this->testCase->assertNull($response->headers->get('Content-Type'));
        };

        $this->assertions["against"] = function () {
            $response = $this->client->getResponse();
            $this->testCase->assertEmpty($content = $response->getContent(), "Response raw content:\n" . $content);
        };

        return $this;
    }

    public function expectingStatusCode($statusCode)
    {
        $this->assertions['statusCode'] = function () use ($statusCode) {
            $this->testCase->assertSame(
                $statusCode,
                $this->client->getResponse()->getStatusCode(),
                "Response raw content:\n" . $this->client->getResponse()->getContent()
            );
        };

        return $this;
    }

    public function asserting($callBack)
    {
        $this->assertions[] = $callBack;

        return $this;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->client->getKernel()->shutdown();
        $this->client->getKernel()->boot();
        $this->eventDispatcher->dispatch(static::EVENT_PRE_REQUEST, $event = new RequestHelperEvent($this));

        $this->client->request($this->method, $this->uri, array(), array(), $this->servers, $event->getBody());

        $this->eventDispatcher->dispatch(static::EVENT_POST_REQUEST, new RequestHelperEvent($this));

       // $this->eventDispatcher->dispatch(static::EVENT_ASSERT, new RequestHelperEvent($this));
        foreach (array_filter($this->assertions) as $callback) {
            call_user_func($callback, $this);
        }

        return $this;
    }

    public function setServerParameter($name, $value)
    {
        $this->servers[$name] = $value;

        return $this;
    }

    /**
     * @param PHPUnit_Framework_TestCase $testCase
     * @param Client $client
     * @return static
     */
    public static function factory(PHPUnit_Framework_TestCase $testCase, Client $client)
    {
        return new static($testCase, $client);
    }
}