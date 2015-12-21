<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\JsonHelper;
use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonHelperTest extends WebTestCase
{
    /**
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testFactory
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testProvideTestFluentHttpMethodUri
     */
    public function testJsonHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $result = $requestHelper->get('/test')
            ->jsonHelper()
            ->executeAndJsonDecode();

        $client = $requestHelper->getClient();

        $this->assertSame('json', $client->getRequest()->getContentType());

        $this->assertSame(
            array('application/json'),
            $client->getRequest()->getAcceptableContentTypes()
        );

        $this->assertSame(array('key' => 'value'), $result);
    }

    public function testPropertyHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $requestHelper->get('/test')
            ->jsonHelper()
            ->propertyHelper()->setPath('key')->attach()
            ->execute();
    }

    /**
     * @depends testJsonHelper
     */
    public function testJsonDecodeMutator()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $jsonHelper = $requestHelper->get('/test')
            ->jsonHelper();

        $this->assertTrue($jsonHelper->getJsonDecodeAssoc(), 'Default value set to true');

        $this->assertSame(
            $jsonHelper,
            $jsonHelper->setJsonDecodeAssoc(false),
            'Fluent interface on setJsonDecodeAssoc'
        );

        $this->assertFalse($jsonHelper->getJsonDecodeAssoc(), 'Value change after setter');

        $this->assertInstanceOf('stdClass', $result = $jsonHelper->executeAndJsonDecode(), 'Json decode as object');

        $this->assertObjectHasAttribute('key', $result);
        $this->assertSame('value', $result->key);
    }

    /**
     * @depends testJsonHelper
     */
    public function testWithBody()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());

        $result = $jsonHelper = $requestHelper->get('/return-json-body')
            ->jsonHelper()
            ->withBody($expected = array('custom' => uniqid()))
            ->executeAndJsonDecode();

        $this->assertSame($expected, $result);
    }

    /**
     * @depends testJsonHelper
     */
    public function testGetName()
    {
        $this->assertSame('json', JsonHelper::getName());
    }
}