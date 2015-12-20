<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Component\EventDispatcher\GenericEvent;

class RequestHelperEvent extends GenericEvent
{
    /**
     * @return RequestHelper
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    /**
     * @return RequestHelper
     */
    public function getRequestHelper()
    {
        return $this->getSubject();
    }

    public function setBody($body)
    {
        $this->setArgument('body', $body);
    }

    public function getBody()
    {
        return $this->hasArgument('body') ? $this->getArgument('body') : null;
    }
}