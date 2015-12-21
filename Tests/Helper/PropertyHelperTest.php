<?php

namespace Draw\Bundle\DrawTestHelperBundle\Tests\Helper;

use Draw\Bundle\DrawTestHelperBundle\Helper\PropertyHelper;
use Draw\Bundle\DrawTestHelperBundle\Helper\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyHelperTest extends WebTestCase
{
    /**
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testFactory
     * @depends Draw\Bundle\DrawTestHelperBundle\Tests\Helper\RequestHelperTest::testProvideTestFluentHttpMethodUri
     */
    public function testPropertyHelper()
    {
        $requestHelper = RequestHelper::factory($this, static::createClient());
        $propertyHelper = $this->createPropertyHelper($requestHelper);

        $this->assertInstanceOf('Draw\Bundle\DrawTestHelperBundle\Helper\PropertyHelper', $propertyHelper);
        $this->assertNotSame($propertyHelper, $this->createPropertyHelper($requestHelper));
    }

    /**
     * @depends testPropertyHelper
     */
    public function testPathMutator()
    {
        $propertyHelper = $this->createPropertyHelper();

        $this->assertNull($propertyHelper->getPath());

        $this->assertSame($propertyHelper, $propertyHelper->setPath('test'), 'Fluent interface failed');

        $this->assertSame('test', $propertyHelper->getPath());
    }

    public function testDoesNotExistsMutator()
    {
        $propertyHelper = $this->createPropertyHelper();

        $this->assertFalse($propertyHelper->getDoesNotExists());

        $this->assertSame($propertyHelper, $propertyHelper->setDoesNotExists(true), 'Fluent interface failed');

        $this->assertTrue($propertyHelper->getDoesNotExists());
    }

    /**
     * @depends testPathMutator
     * @depends testDoesNotExistsMutator
     */
    public function testAssertDoesNotExistsFailed()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            "Property does exists.\nProperty path: test\nData:\n{\n    \"test\": \"value\"\n}\nBe careful for assoc array and object"
        );

        $propertyHelper = $this->createPropertyHelper();

        $propertyHelper->setPath('test')->setDoesNotExists(true)->assert((object)array('test' => 'value'));
    }

    /**
     * @depends testPathMutator
     * @depends testDoesNotExistsMutator
     */
    public function testAssertDoesNotExists()
    {
        $propertyHelper = $this->createPropertyHelper();

        $propertyHelper->setPath('test')->setDoesNotExists(true)->assert((object)array());
    }

    /**
     * @depends testPathMutator
     * @depends testDoesNotExistsMutator
     */
    public function testAssertDoesExistsFailed()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            "Property does not exists.\nProperty path: test\nData:\n{}\nBe careful for assoc array and object"
        );

        $propertyHelper = $this->createPropertyHelper();

        $propertyHelper->setPath('test')->assert((object)array());
    }

    /**
     * @depends testPathMutator
     * @depends testDoesNotExistsMutator
     */
    public function testAssertDoesExists()
    {
        $propertyHelper = $this->createPropertyHelper();

        $propertyHelper->setPath('test')->assert((object)array('test' => 'value'));
    }

    public function provideTestAssert()
    {
        $data = (object)array('test' => 'value');
        $exception = 'PHPUnit_Framework_ExpectationFailedException';

        return array(
            array('test', $data, 'assertSame', array('value'), null),
            array('test', $data, 'assertNotSame', array('value'), $exception)
        );
    }

    /**
     * @dataProvider provideTestAssert
     */
    public function testAssert($path, $data, $assert, array $arguments, $expectedException)
    {
        $propertyHelper = $this->createPropertyHelper();
        $propertyHelper->setPath($path);

        if (!is_null($expectedException)) {
            $this->setExpectedException($expectedException);
        }

        $this->assertSame(
            $propertyHelper,
            call_user_func_array(array($propertyHelper, $assert), $arguments),
            'Fluent interface failed'
        );

        $propertyHelper->assert($data);
    }

    /**
     * @depends testPropertyHelper
     */
    public function testGetName()
    {
        $this->assertSame('property', PropertyHelper::getName());
    }

    /**
     * @param RequestHelper $requestHelper
     * @return PropertyHelper
     */
    private function createPropertyHelper(RequestHelper $requestHelper = null)
    {
        if (is_null($requestHelper)) {
            $requestHelper = RequestHelper::factory($this, static::createClient());
        }

        return PropertyHelper::instantiate($requestHelper);
    }
}