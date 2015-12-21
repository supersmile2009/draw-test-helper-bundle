<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Bridge\Monolog\Handler\DebugHandler;

class LogHelper extends BaseRequestHelper
{
    /**
     * @var integer
     */
    private $level;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $count;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var PropertyHelper[]
     */
    private $contextPropertyHelpers = array();

    protected function initialize()
    {
        $this->requestHelper->asserting(array($this, 'assert'));
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @param $propertyPath
     * @return PropertyHelper
     */
    public function contextPropertyHelper()
    {
        $this->contextPropertyHelpers[] = $propertyHelper = PropertyHelper::instantiate($this->requestHelper);
        return $propertyHelper;
    }

    public function assert()
    {
        $logger = $this->getDebugLogger();

        $records = $logger->getRecords();

        $records = array_filter(
            $records,
            function ($record) {
                return $record['message'] == $this->getMessage();
            }
        );

        $testCase = $this->requestHelper->getTestCase();

        if (!is_null($this->getCount())) {
            $testCase->assertCount($this->getCount(), $records, 'Improper count');
        } else {
            $testCase->assertNotEmpty($records, 'Log not found');
        }

        foreach ($records as $record) {
            if ($this->getLevel()) {
                $testCase->assertSame($this->getLevel(), $record['level'], 'Improper level');
            }

            if ($this->getChannel()) {
                $testCase->assertSame($this->getChannel(), $record['channel'], 'Improper channel');
            }

            foreach($this->contextPropertyHelpers as $propertyHelper) {
                $propertyHelper->assert($record['context']);
            }
        }
    }

    /**
     * @return DebugHandler
     */
    private function getDebugLogger()
    {
        $handlers = $logs = $this->requestHelper->getClient()->getContainer()->get('logger')->getHandlers();
        $found = null;
        foreach ($handlers as $handler) {
            if ($handler instanceof DebugHandler) {
                $found = $handler;
                break;
            }
        }

        $this->requestHelper->getTestCase()
            ->assertNotNull(
                $found,
                "Symfony\Bridge\Monolog\Handler\DebugHandler not found.\n" .
                'Make sure the configuration { framework: { profiler: {} } } is active.'
            );

        return $found;
    }

    /**
     * Return the name of the request helper
     *
     * @return string
     */
    static public function getName()
    {
        return 'log';
    }


}