<?php

namespace TestBundle\Controller;

use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/no-content")
     *
     * @return JsonResponse
     */
    public function noContentAction()
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/with-content")
     *
     * @return JsonResponse
     */
    public function withContentAction()
    {
        return new Response('content');
    }

    /**
     * @Route("/with-content-no-type")
     *
     * @return JsonResponse
     */
    public function withContentNoTypeAction()
    {
        $response = new Response('content');
        $response->headers->set("Content-Type", null);

        return $response;
    }

    /**
     * @Route("/return-json-body")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function returnJsonBodyAction(Request $request)
    {
        return new JsonResponse(json_decode($request->getContent()));
    }

    /**
     * @Route("/log")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logAction(Request $request)
    {
        $channel = $request->query->get('channel');
        $level = $request->query->get('level', Logger::INFO);
        $context = $request->query->get('context', []);
        $message = $request->query->get('message', 'message');
        $count = $request->query->get('count',1);

        if(is_null($channel)) {
            $logger = $this->get("logger");
        } else {
            $logger = $this->get("monolog.logger." . $channel);
        }

        for($i = 0 ; $i < $count ; $i++) {
            $logger->log((int)$level, $message, $context);
        }

        return new Response($message);
    }
}