<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

class JsonHelper extends BaseRequestHelper
{
    /**
     * The body to json_encode and send as the request
     *
     * @var mixed
     */
    private $body;

    /**
     * Use assoc on json decode
     *
     * The default value is true since it's easier to validate with assertSame when using array
     *
     * @see RequestHelper::executeAndJsonDecode
     *
     * @var boolean
     */
    private $jsonDecodeAssoc = true;

    /**
     * @var PropertyHelper[]
     */
    private $propertyHelpers = array();

    /**
     * @var null|string
     */
    private $expectedJsonString = null;

    /**
     * @return boolean
     */
    public function getJsonDecodeAssoc()
    {
        return $this->jsonDecodeAssoc;
    }

    /**
     * @param boolean $jsonDecodeAssoc
     *
     * @return $this
     */
    public function setJsonDecodeAssoc($jsonDecodeAssoc)
    {
        $this->jsonDecodeAssoc = $jsonDecodeAssoc;

        return $this;
    }

    protected function initialize()
    {
        $this->requestHelper->setServerParameter('HTTP_ACCEPT', 'application/json');
        $this->requestHelper->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->requestHelper->expectContentType('application/json');
        $this->requestHelper->asserting(array($this, 'assert'));

        $this->requestHelper->addListener(
            RequestHelper::EVENT_PRE_REQUEST,
            function (RequestHelperEvent $event) {
                if (!is_null($this->body)) {
                    $event->setBody(json_encode($this->body));
                }
            }
        );
    }

    public function assert(RequestHelper $requestHelper)
    {
        if($this->expectedJsonString) {
            $requestHelper->getTestCase()
                ->assertJsonStringEqualsJsonString(
                    $this->expectedJsonString,
                    $requestHelper->getClient()->getResponse()->getContent()
                );
        }

        $data = $this->jsonDecode($requestHelper, false);
        foreach ($this->propertyHelpers as $propertyHelper) {
            $propertyHelper->assert($data);
        }
    }

    private function jsonDecode(RequestHelper $requestHelper, $assoc)
    {
        return json_decode($requestHelper->getClient()->getResponse()->getContent(), $assoc);
    }

    public function executeAndJsonDecode()
    {
        return $this->jsonDecode($this->requestHelper->execute(), $this->jsonDecodeAssoc);
    }

    public function assertContentEqualsJsonString($expectedJsonString)
    {
        $this->expectedJsonString = $expectedJsonString;

        return $this;
    }

    public function assertContentEqualsJsonFile($fileName)
    {
        $this->expectedJsonString = file_get_contents($fileName);

        return $this;
    }

    /**
     * @return JsonRequestPropertyHelper
     */
    public function propertyHelper($path = null)
    {
        $this->propertyHelpers[] = $propertyHelper = JsonRequestPropertyHelper::instantiate($this->requestHelper);

        if(!is_null($path)) {
            $propertyHelper->setPath($path);
        }
        return $propertyHelper;
    }

    /**
     * @param $body
     * @return $this
     */
    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Return the name of the request helper
     *
     * @return string
     */
    static public function getName()
    {
        return 'json';
    }
}