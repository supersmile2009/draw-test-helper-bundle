<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\LogHelper;
use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Monolog\Logger;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bridge\Monolog\Handler\DebugHandler;
use Symfony\Bridge\Monolog\Processor\DebugProcessor;
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
     * @expectedExceptionMessage Log not found
     */
    public function testMessageFailed()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?message=toto')
            ->logHelper()->setMessage('somethingElse')->attach()
            ->execute();
    }

    /**
     * @depends testLogHelper
     */
    public function testMessage()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?message=toto')
            ->logHelper()->setMessage('toto')->attach()
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
    public function testContextPropertyHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/log?context[test]=test')
            ->logHelper()
                ->setMessage('message')
                ->contextPropertyHelper()->setPath('[test]')->attach()
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
            if ($handler instanceof DebugProcessor) {
                unset($handlers[$index]);
            }
        }

        $logger->setHandlers($handlers);

        $this->expectException(AssertionFailedError::class);

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