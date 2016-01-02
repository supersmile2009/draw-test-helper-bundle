<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class JsonRequestPropertyHelper extends PropertyHelper
{
    /**
     * @return JsonHelper
     */
    public function end()
    {
        return $this->requestHelper->jsonHelper();
    }
}