<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class SqlHelper
{
    private $requestHelper;

    public function __construct(RequestHelper $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * @param $amount
     */
    public function maximumSqlQuery($amount)
    {
        if (!$this->maximumSqlQuery) {
            $this->addPreRequestCallback(
                function () {
                    $this->client->getKernel()->boot();
                    $this->client->enableProfiler();
                }
            );
        }

        $this->maximumSqlQuery = $amount;
        $this->asserting(
            function () {
                $queries = $this->client->getProfile()->getCollector('db')->getQueries()['default'];
                //We remove the query "COMMIT" and "START TRANSACTION"
                $queries = array_filter(
                    $queries,
                    function ($query) {
                        return !is_null($query['types']);
                    }
                );

                $this->testCase->assertLessThanOrEqual(
                    $this->maximumSqlQuery,
                    count($queries),
                    json_encode($queries, JSON_PRETTY_PRINT)
                );
            }
        );

        return $this;
    }
}