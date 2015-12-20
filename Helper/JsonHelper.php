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

        $this->requestHelper->addListener(
            RequestHelper::EVENT_PRE_REQUEST,
            function(RequestHelperEvent $event) {


                if (!is_null($this->body)) {
                    $event->setBody(json_encode($this->body));
                }
            }
        );
    }

    public function executeAndJsonDecode()
    {
        $content = $this->requestHelper->execute()->getClient()->getResponse()->getContent();
        return json_decode($content, $this->jsonDecodeAssoc);
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