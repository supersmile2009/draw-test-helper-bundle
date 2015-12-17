<?php

namespace Draw\Bundle\DrawTestHelperBundle\Helper;

use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyHelper
{
    public $propertyPath;

    public $type;

    public $checkIsSameValue = false;

    public $value;

    public $mustReplaceValue = false;

    public $replaceWithValue;

    public $in = [];

    public $parentPropertyHelper;

    public $notSameAs = [];

    public $doesNotExists;

    /**
     * @var RequestHelper
     */
    public $requestHelper;

    public function __construct(RequestHelper $requestHelper, $propertyPath)
    {
        $this->requestHelper = $requestHelper;
        $this->propertyPath = $propertyPath;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()->getPropertyAccessor();
    }

    public function isOfType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function isSameAs($value)
    {
        $this->checkIsSameValue = true;
        $this->value = $value;

        return $this;
    }

    public function doesNotExists()
    {
        $this->doesNotExists = true;

        return $this;
    }

    public function notSameAs($value)
    {
        $this->notSameAs[] = $value;

        return $this;
    }

    public function replace($with = 'checked by Draw\DrawBundle\Test\PropertyHelper')
    {
        $this->mustReplaceValue = true;
        $this->replaceWithValue = $with;

        return $this;
    }

    /**
     * @param $filterCallBack
     * @return PropertyHelper
     */
    public function in($filterCallBack, $match = 1)
    {
        $this->in[] = [$filterCallBack, $match, $propertyHelper = new PropertyHelper($this->requestHelper, '')];
        $this->type = 'array';
        $propertyHelper->parentPropertyHelper = $this;

        return $propertyHelper;
    }

    /**
     * @return PropertyHelper
     */
    public function end()
    {
        return $this->parentPropertyHelper;
    }

    public function attach()
    {
        $this->requestHelper->contentFilters[] = array($this, 'assert');

        return $this->requestHelper;
    }

    protected function assertData($data)
    {
        $testCase = $this->requestHelper->testCase;

        if($this->doesNotExists) {
            $testCase->assertFalse(
                $this->propertyAccessor->isReadable($data, $this->propertyPath),
                "Property does exists.\nProperty path: " . $this->propertyPath . "\nData:" .
                json_encode($data, JSON_PRETTY_PRINT)
            );

            return $data;
        }

        $testCase->assertTrue(
            $this->propertyAccessor->isReadable($data, $this->propertyPath),
            "Property does not exists.\nProperty path: " . $this->propertyPath . "\nData:" .
            json_encode($data, JSON_PRETTY_PRINT)
        );

        $value = $this->propertyAccessor->getValue($data, $this->propertyPath);

        if ($this->type) {
            $testCase->assertInternalType($this->type, $value, 'Property path: ' . $this->propertyPath);
        }

        foreach ($this->in as $in) {
            list($filterCallback, $match, $propertyHelper) = $in;
            $currentMatch = 0;
            /* @var PropertyHelper $propertyHelper */
            foreach ($value as $key => $subValue) {
                if (!call_user_func($filterCallback, $subValue)) {
                    continue;
                }
                $currentMatch++;
                $propertyHelper->propertyPath = $this->propertyPath . '[' . $key . ']';
                $decodedData = json_decode($propertyHelper->assert(json_encode($data)));
            }

            $testCase->assertSame($match, $currentMatch, 'The amount of item found does not match in [' . $this->propertyPath . ']');
        }

        if ($this->checkIsSameValue) {
            $testCase->assertJsonStringEqualsJsonString(json_encode($this->value), json_encode($value), 'Property path: ' . $this->propertyPath);
        }

        foreach($this->notSameAs as $notSameValue) {
            $testCase->assertNotSame($notSameValue, $value, 'Property path: ' . $this->propertyPath);
        }

        if ($this->mustReplaceValue) {
            $this->propertyAccessor->setValue($decodedData, $this->propertyPath, $this->replaceWithValue);
        }

        return $data;
    }

    public function assert($data)
    {
        $decodedData = json_decode($data);

        $decodedData = $this->assertData($decodedData);

        return json_encode($decodedData);
    }
}