<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Type;

use Generator;
use Meritoo\Common\Test\Base\BaseTypeTestCase;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the type of method used while talking with LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MethodTypeTest extends BaseTypeTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertHasNoConstructor(MethodType::class);
    }

    /**
     * @param string $incorrectMethod Type of method to verify
     * @dataProvider provideIncorrectMethod
     */
    public function testGetValidatedMethodWithIncorrectMethod($incorrectMethod)
    {
        $this->expectException(UnknownMethodException::class);
        MethodType::getValidatedMethod($incorrectMethod);
    }

    /**
     * @param string $method Type of method to verify
     * @dataProvider provideMethod
     */
    public function testGetValidatedMethod($method)
    {
        static::assertEquals($method, MethodType::getValidatedMethod($method));
    }

    /**
     * @param string $incorrectMethod Type of incorrectMethod to verify
     * @dataProvider provideIncorrectMethod
     */
    public function testIsResultIterableWithIncorrectMethod($incorrectMethod)
    {
        $this->expectException(UnknownMethodException::class);
        MethodType::isResultIterable($incorrectMethod);
    }

    /**
     * @param string $method   Type of method to verify
     * @param bool   $expected Information if result provided by the API is iterable
     *
     * @dataProvider provideIterableType
     */
    public function testIsResultIterable($method, $expected)
    {
        static::assertEquals($expected, MethodType::isResultIterable($method));
    }

    /**
     * Provides correct type of method
     *
     * @return Generator
     */
    public function provideMethod()
    {
        yield[
            MethodType::ADD_RESPONSE,
        ];

        yield[
            MethodType::EXPORT_STATISTICS,
        ];

        yield[
            MethodType::GET_PARTICIPANT_PROPERTIES,
        ];

        yield[
            MethodType::LIST_SURVEYS,
        ];
    }

    /**
     * Provides incorrect type of method
     *
     * @return Generator
     */
    public function provideIncorrectMethod()
    {
        yield[
            '',
        ];

        yield[
            null,
        ];

        yield[
            true,
        ];

        yield[
            false,
        ];

        yield[
            'lorem',
        ];
    }

    /**
     * Provides type of method who result provided by the API is iterable and information if it's iterable
     *
     * @return Generator
     */
    public function provideIterableType()
    {
        yield[
            MethodType::ADD_RESPONSE,
            false,
        ];

        yield[
            MethodType::GET_PARTICIPANT_PROPERTIES,
            false,
        ];

        yield[
            MethodType::LIST_PARTICIPANTS,
            true,
        ];

        yield[
            MethodType::LIST_QUESTIONS,
            true,
        ];

        yield[
            MethodType::LIST_SURVEYS,
            true,
        ];

        yield[
            MethodType::LIST_USERS,
            true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllExpectedTypes()
    {
        return [
            'ADD_RESPONSE'               => MethodType::ADD_RESPONSE,
            'EXPORT_STATISTICS'          => MethodType::EXPORT_STATISTICS,
            'GET_PARTICIPANT_PROPERTIES' => MethodType::GET_PARTICIPANT_PROPERTIES,
            'GET_QUESTION_PROPERTIES'    => MethodType::GET_QUESTION_PROPERTIES,
            'LIST_PARTICIPANTS'          => MethodType::LIST_PARTICIPANTS,
            'LIST_QUESTIONS'             => MethodType::LIST_QUESTIONS,
            'LIST_SURVEYS'               => MethodType::LIST_SURVEYS,
            'LIST_USERS'                 => MethodType::LIST_USERS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTestedTypeInstance()
    {
        return new MethodType();
    }

    /**
     * {@inheritdoc}
     */
    public function provideTypeToVerify()
    {
        yield[
            '',
            false,
        ];

        yield[
            'lorem',
            false,
        ];

        yield[
            MethodType::ADD_RESPONSE,
            true,
        ];

        yield[
            MethodType::GET_PARTICIPANT_PROPERTIES,
            true,
        ];
    }
}
