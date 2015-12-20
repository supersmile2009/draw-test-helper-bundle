<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

interface RequestHelperInterface
{
    /**
     * Return the initial request helper so it can have a fluent interface.
     *
     * @return RequestHelper
     */
    public function attach();

    /**
     * Return the name of the request helper
     *
     * @return string
     */
    static public function getName();

    /**
     * Return a instance of himself.
     *
     * Sometime only one helper of a specific type must be set for a request so the same instance can be return
     * if the same request helper is used. This method should always be used instead of the constructor.
     *
     * @param RequestHelper $requestHelper
     * @return static
     */
    static public function instantiate(RequestHelper $requestHelper);
}