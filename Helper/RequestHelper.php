<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RequestHelper
{
    const EVENT_NEW_HELPER = "requestHelper.newHelper";

    const EVENT_PRE_REQUEST = "requestHelper.preRequest";

    const EVENT_POST_REQUEST = "requestHelper.postRequest";

    /**
     * @var TestCase
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

    /**
     * @var string|null
     */
    private $body;

    private $parameters = [];

    private $queryFilters = [];

    /**
     * A list of <filename,path> to upload to the server
     *
     * @var <string,string>
     */
    private $files = [];

    public $assertions = [
        "statusCode" => null,
        "responseContentType" => null,
        "against" => null,
    ];

    private $servers = [];

    public function __construct(TestCase $testCase, Client $client)
    {
        $this->client = $client;
        $this->testCase = $testCase;
        $this->eventDispatcher = new EventDispatcher();
        $this->expectingStatusCode(200);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getParameter($name)
    {
        return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return TestCase
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
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function head($uri = null, $expectedStatus = null)
    {
        $this->setMethod('HEAD');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to GET and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function get($uri = null, $expectedStatus = null)
    {
        $this->setMethod('GET');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to POST and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function post($uri = null, $expectedStatus = null)
    {
        $this->setMethod('POST');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to PUT and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function put($uri = null, $expectedStatus = null)
    {
        $this->setMethod('PUT');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to PATCH and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function patch($uri = null, $expectedStatus = null)
    {
        $this->setMethod('PATCH');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to DELETE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function delete($uri = null, $expectedStatus = null)
    {
        $this->setMethod('DELETE');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to PURGE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function purge($uri = null, $expectedStatus = null)
    {
        $this->setMethod('PURGE');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to OPTIONS and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function options($uri = null, $expectedStatus = null)
    {
        $this->setMethod('OPTIONS');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to TRACE and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function trace($uri = null, $expectedStatus = null)
    {
        $this->setMethod('TRACE');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * Set the HTTP method to CONNECT and the uri if any.
     *
     * Return $this for a fluent interface.
     *
     * @param string|null $uri
     * @param integer|null $expectedStatus
     * @return $this
     */
    public function connect($uri = null, $expectedStatus = null)
    {
        $this->setMethod('CONNECT');
        $this->setUri($uri);
        if (!is_null($expectedStatus)) {
            $this->expectingStatusCode($expectedStatus);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the default body
     *
     * @param null|string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param $name
     * @param $filePath
     * @param $originalName
     * @param $mimeType
     * @param int $error
     * @return RequestHelper
     */
    public function addFile($name, $filePath, $originalName, $mimeType = null, $error = null)
    {
        return $this->addUpdatedFile(
            $name,
            new UploadedFile(
                $filePath,
                $originalName,
                $mimeType,
                filesize($filePath),
                $error,
                true
            )
        );
    }

    /**
     * @param $name
     * @param UploadedFile $uploadedFile
     * @return $this
     */
    public function addUpdatedFile($name, UploadedFile $uploadedFile)
    {
        $this->files[$name] = $uploadedFile;

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
    public function sqlHelper($maximumQueryCount = null, $filters = [])
    {
        $sqlHelper = SqlHelper::instantiate($this);
        $sqlHelper->setQueryFilters($filters);

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
        $event = new RequestHelperEvent($this);
        $event->setBody($this->body);
        $this->eventDispatcher->dispatch(static::EVENT_PRE_REQUEST, $event);

        $this->client->request(
            $this->method,
            $this->uri,
            $this->parameters,
            $this->files,
            $this->servers,
            $event->getBody()
        );

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
     * @param TestCase $testCase
     * @param Client $client
     * @return static
     */
    public static function factory(TestCase $testCase, Client $client)
    {
        return new static($testCase, $client);
    }
}