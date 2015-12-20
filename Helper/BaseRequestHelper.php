<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

abstract class BaseRequestHelper implements RequestHelperInterface
{
    private static $instances = [];

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

        if(static::isSingleInstance()) {
            static::$instances[$objectHash][static::getName()] = $instance;
        }

        return $instance;
    }
}