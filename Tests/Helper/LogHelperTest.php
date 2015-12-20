<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\LogHelper;
use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Handler\DebugHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogHelperTest extends WebTestCase
{
    /**
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testLogHelper
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testProvideTestFluentHttpMethodUri
     */
    public function testLogHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log')
            ->logHelper()->setMessage('message')->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     *
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Improper channel
     */
    public function testChannelFailed()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log')
            ->logHelper()->setMessage('message')->setChannel('channel')->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testChannel()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?channel=channel')
            ->logHelper()->setMessage('message')->setChannel('channel')->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     *
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Improper level
     */
    public function testLevelFailed()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log')
            ->logHelper()->setMessage('message')->setLevel(Logger::CRITICAL)->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testLevel()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?level=' . Logger::CRITICAL)
            ->logHelper()->setMessage('message')->setLevel(Logger::CRITICAL)->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     *
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Improper count
     */
    public function testCountFailed()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log')
            ->logHelper()->setMessage('message')->setCount(2)->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testCount()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?count=2')
            ->logHelper()->setMessage('message')->setCount(2)->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testNoDebugHandler()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $logger = $requestHelper->getClient()->getContainer()->get("logger");

        $handlers = $logger->getHandlers();

        foreach ($handlers as $index => $handler) {
            if ($handler instanceof DebugHandler) {
                unset($handlers[$index]);
            }
        }

        $logger->setHandlers($handlers);

        $this->setExpectedException(
            'PHPUnit_Framework_AssertionFailedError',
            "Symfony\Bridge\Monolog\Handler\DebugHandler not found.\nMake sure the configuration { framework: { profiler: {} } } is active."
        );

        $requestHelper->get('/log?count=2')
            ->logHelper()->setMessage('message')->setCount(2)->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testGetName()
    {
        $this->assertSame('log', LogHelper::getName());
    }
}