<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Draw\Bundle\DrawTestHelperBundle\Helper\SqlHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SqlHelperTest extends WebTestCase
{
    /**
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testSqlHelper
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testProvideTestFluentHttpMethodUri
     */
    public function testSqlHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $helper = $requestHelper->sqlHelper();
        $this->assertSame($helper, $requestHelper->sqlHelper(), 'Failed using the same instance');
    }

    /**
     * @depends testSqlHelper
     *
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Maximum query count exceeded
     */
    public function testMaximumQueryCountFailed()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/create-entity')
            ->sqlHelper()->attach()
            ->execute();
    }

    /**
     * @depends testSqlHelper
     */
    public function testMaximumQueryCount()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/create-entity')
            ->sqlHelper()->setMaximumQueryCount(1)->attach()
            ->execute();
    }

    /**
     * @depends testMaximumQueryCountFailed
     *
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Maximum query count exceeded
     */
    public function testFilterTransactionQuery()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/create-entity')
            ->sqlHelper()->setFilterTransactionQuery(false)->setMaximumQueryCount(1)->attach()
            ->execute();
    }

    /**
     * @depends testSqlHelper
     */
    public function testGetName()
    {
        $this->assertSame('sql', SqlHelper::getName());
    }
}