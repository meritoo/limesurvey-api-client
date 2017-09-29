<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Exception;
use Generator;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\InvalidResultOfMethodRunException;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of an exception used when an error occurred while running method
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class InvalidResultOfMethodRunExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(InvalidResultOfMethodRunException::class, OopVisibilityType::IS_PUBLIC, 3, 2);
    }

    /**
     * @param Exception $previousException The previous exception, source of an error
     * @param string    $methodName        Name of called method
     * @param array     $methodArguments   Arguments of the called method
     * @param string    $expectedMessage   Expected exception's message
     *
     * @dataProvider providePreviousExceptionAndMethod
     */
    public function testConstructorMessage(Exception $previousException, $methodName, array $methodArguments, $expectedMessage)
    {
        $exception = new InvalidResultOfMethodRunException($previousException, $methodName, $methodArguments);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides previous exception, name and arguments of called method
     *
     * @return Generator
     */
    public function providePreviousExceptionAndMethod()
    {
        $template = "Oops, an error occurred while running method. Is there everything ok? Details:\n"
            . "- error: %s,\n"
            . "- method: %s,\n"
            . '- arguments: %s.';

        yield[
            new Exception('Lorem ipsum'),
            MethodType::ADD_RESPONSE,
            [],
            sprintf($template, 'Lorem ipsum', MethodType::ADD_RESPONSE, '(no arguments)'),
        ];

        yield[
            new Exception('Dolor sit amet'),
            MethodType::LIST_SURVEYS,
            [
                'fist_name' => 'John',
                'last_name' => 'Scott',
                'email'     => 'john@scott.com',
            ],
            sprintf($template, 'Dolor sit amet', MethodType::LIST_SURVEYS, 'fist_name="John", last_name="Scott", email="john@scott.com"'),
        ];
    }
}
