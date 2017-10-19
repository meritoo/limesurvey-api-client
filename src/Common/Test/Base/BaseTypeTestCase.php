<?php

namespace Meritoo\Common\Test\Base;

use Generator;
use Meritoo\Common\Type\Base\BaseType;

/**
 * Base test case for the type of something
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseTypeTestCase extends BaseTestCase
{
    /**
     * Verifies availability of all types
     */
    public function testAvailabilityOfAllTypes()
    {
        $available = $this->getTestedTypeInstance()->getAll();
        $all = $this->getAllExpectedTypes();

        if (isset($available['className'])) {
            unset($available['className']);
        }

        static::assertEquals($all, $available);
    }

    /**
     * Verifies whether given type is correct or not
     *
     * @param string $type     Type to verify
     * @param bool   $expected Information if given type is correct or not
     *
     * @dataProvider provideTypeToVerify
     */
    public function testIfGivenTypeIsCorrect($type, $expected)
    {
        static::assertEquals($expected, $this->getTestedTypeInstance()->isCorrectType($type));
    }

    /**
     * Provides type to verify and information if it's correct
     *
     * @return Generator
     */
    abstract public function provideTypeToVerify();

    /**
     * Returns instance of the tested type
     *
     * @return BaseType
     */
    abstract protected function getTestedTypeInstance();

    /**
     * Returns all expected types of the tested type
     *
     * @return array
     */
    abstract protected function getAllExpectedTypes();
}
