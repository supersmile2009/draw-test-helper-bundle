<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyHelper extends BaseRequestHelper
{
    /**
     * String property path compatible with Symfony\Component\PropertyAccess\PropertyAccessor
     *
     * @see http://symfony.com/doc/2.3/components/property_access/introduction.html
     *
     * @var string
     */
    private $path;

    /**
     * @var boolean
     */
    private $doesNotExists = false;

    /**
     * @var array
     */
    private $assertions = [];

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;


    protected function initialize()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()->getPropertyAccessor();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * return $this;
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDoesNotExists()
    {
        return $this->doesNotExists;
    }

    /**
     * @param boolean $doesNotExists
     *
     * return $this;
     */
    public function setDoesNotExists($doesNotExists)
    {
        $this->doesNotExists = $doesNotExists;

        return $this;
    }

    public function assert($data)
    {
        $testCase = $this->requestHelper->getTestCase();

        if ($this->getDoesNotExists()) {
            $testCase->assertFalse(
                $this->propertyAccessor->isReadable($data, $this->path),
                "Property does exists.\nProperty path: " . $this->path . "\nData:\n" .
                json_encode($data, JSON_PRETTY_PRINT) . "\nBe careful for assoc array and object"
            );

            return $data;
        }

        $testCase->assertTrue(
            $this->propertyAccessor->isReadable($data, $this->path),
            "Property does not exists.\nProperty path: " . $this->path . "\nData:\n" .
            json_encode($data, JSON_PRETTY_PRINT) . "\nBe careful for assoc array and object"
        );

        $value = $this->propertyAccessor->getValue($data, $this->path);

        foreach ($this->assertions as $assertion) {
            list($method, $arguments) = $assertion;
            //We insert the value at position 1 since the assert* function in phpunit
            //always take the value as a second argument
            array_splice($arguments, 1, 0, $value);
            call_user_func_array(array($testCase, $method), $arguments);
        }
    }

    /**
     * @param $method
     * @param array $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments = array())
    {
        $this->assertions[] = [$method, $arguments];

        return $this;
    }

    /**
     * Return the name of the request helper
     *
     * @return string
     */
    static public function getName()
    {
        return 'property';
    }


}