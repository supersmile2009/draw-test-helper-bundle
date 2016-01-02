<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

abstract class BaseRequestHelper implements RequestHelperInterface
{
    protected static $instances = [];

    /**
     * @var RequestHelper
     */
    protected $requestHelper;

    private function __construct()
    {

    }

    protected function initialize()
    {

    }

    static public function isSingleInstance()
    {
        return false;
    }

    /**
     * Return the parent in the call tree
     *
     * @return RequestHelper|mixed
     */
    public function end()
    {
        return $this->requestHelper;
    }

    /**
     * Return the initial request helper so it can have a fluent interface.
     *
     * @return RequestHelper
     */
    public function attach()
    {
        return $this->requestHelper;
    }

    /**
     * Return a instance of himself.
     *
     * Sometime only one helper of a specific type must be set for a request so the same instance can be return
     * if the same request helper is used. This method should always be used instead of the constructor.
     *
     * @param RequestHelper $requestHelper
     * @return static
     */
    static public function instantiate(RequestHelper $requestHelper)
    {
        $objectHash = spl_object_hash($requestHelper);
        if(static::isSingleInstance()) {
            if(isset(static::$instances[$objectHash][static::getName()])) {
                return static::$instances[$objectHash][static::getName()];
            }
        }

        $instance = new static();
        $instance->requestHelper = $requestHelper;
        $instance->initialize();

        $requestHelper->getEventDispatcher()
            ->dispatch(
                RequestHelper::EVENT_NEW_HELPER,
                new RequestHelperEvent($requestHelper, array('helper' => $instance))
            );

        if(static::isSingleInstance()) {
            static::$instances[$objectHash][static::getName()] = $instance;
        }

        return $instance;
    }
}