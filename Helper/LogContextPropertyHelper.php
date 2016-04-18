<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class LogContextPropertyHelper extends PropertyHelper
{
    /**
     * @var LogHelper
     */
    public $logHelper;

    /**
     * @return LogHelper
     */
    public function end()
    {
        return $this->logHelper;
    }
}