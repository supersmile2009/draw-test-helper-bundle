<?php

namespace TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TestController extends Controller
{
    /**
     * @Route("/test")
     *
     * @return JsonResponse
     */
    public function testAction()
    {
        return new JsonResponse(array('key' => 'value'));
    }
}