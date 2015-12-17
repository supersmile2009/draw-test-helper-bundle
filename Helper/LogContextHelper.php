<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class LogContextHelper extends PropertyHelper
{
    public function __construct(LogHelper $logHelper, $propertyPath)
    {
        $this->logHelper = $logHelper;
        parent::__construct($logHelper->requestHelper, $propertyPath);
    }

    public function attach()
    {
        $this->logHelper->contextAsserts[] = array($this, 'assert');

        return $this->logHelper;
    }

    public function assert($data)
    {
        return $this->assertData($data);
    }
}