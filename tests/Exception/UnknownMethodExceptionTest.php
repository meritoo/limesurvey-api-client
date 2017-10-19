<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of an exception used while name of method used while talking to the LimeSurvey's API is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class UnknownMethodExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(UnknownMethodException::className, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    /**
     * @param string $unknownType     The unknown type of something (value of constant)
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideUnknownType
     */
    public function testConstructorMessage($unknownType, $expectedMessage)
    {
        $exception = new UnknownMethodException($unknownType);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides name of called method
     *
     * @return array
     * //return Generator
     */
    public function provideUnknownType()
    {
        $allMethods = implode(', ', (new MethodType())->getAll());

        $template = 'The \'%s\' type of name of method used while talking to the LimeSurvey\'s API is unknown. Probably'
            . ' doesn\'t exist or there is a typo. You should use one of these types: %s.';

        return [
            [
                MethodType::ADD_PARTICIPANTS,
                sprintf($template, MethodType::ADD_PARTICIPANTS, $allMethods),
            ],
            [
                MethodType::ADD_PARTICIPANTS,
                sprintf($template, MethodType::ADD_PARTICIPANTS, $allMethods),
            ],
        ];

        /*
        yield[
            MethodType::ADD_PARTICIPANTS,
            sprintf($template, MethodType::ADD_PARTICIPANTS, $allMethods),
        ];

        yield[
            MethodType::ADD_PARTICIPANTS,
            sprintf($template, MethodType::ADD_PARTICIPANTS, $allMethods),
        ];
        */
    }
}
