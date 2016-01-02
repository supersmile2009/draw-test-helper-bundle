<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class SqlHelper extends BaseRequestHelper
{
    /**
     * The maximum of query
     *
     * @var integer
     */
    private $maximumQueryCount = 0;

    /**
     * If we must filter out the transaction related query in the count.
     *
     * Transaction of type "COMMIT" and "START TRANSACTION"
     *
     * @var boolean
     */
    private $filterTransactionQuery = true;

    /**
     * @return mixed
     */
    public function getMaximumQueryCount()
    {
        return $this->maximumQueryCount;
    }

    /**
     * @param mixed $maximumQueryCount
     *
     * @return $this;
     */
    public function setMaximumQueryCount($maximumQueryCount)
    {
        $this->maximumQueryCount = $maximumQueryCount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getFilterTransactionQuery()
    {
        return $this->filterTransactionQuery;
    }

    /**
     * @param boolean $filterTransactionQuery
     *
     * @return $this
     */
    public function setFilterTransactionQuery($filterTransactionQuery)
    {
        $this->filterTransactionQuery = $filterTransactionQuery;

        return $this;
    }

    protected function initialize()
    {
        $this->requestHelper->addListener(
            RequestHelper::EVENT_PRE_REQUEST,
            function(RequestHelperEvent $event) {
                $client = $event->getRequestHelper()->getClient();
                $client->getKernel()->boot();
                $client->enableProfiler();

                $event->getRequestHelper()->asserting(
                    function (RequestHelper $requestHelper) {
                        $queries = $requestHelper->getClient()
                            ->getProfile()
                            ->getCollector('db')
                            ->getQueries()['default'];

                        if($this->getFilterTransactionQuery()) {
                            $queries = array_filter(
                                $queries,
                                function ($query) {
                                    return !is_null($query['types']);
                                }
                            );
                        }

                        $requestHelper->getTestCase()->assertLessThanOrEqual(
                            $this->getMaximumQueryCount(),
                            count($queries),
                            "Maximum query count exceeded.\nQueries:\n" .
                            json_encode($queries, JSON_PRETTY_PRINT)
                        );
                    }
                );
            }
        );
    }

    static public function isSingleInstance()
    {
        return true;
    }

    /**
     * Return the name of the request helper
     *
     * @return string
     */
    static public function getName()
    {
        return 'sql';
    }


}