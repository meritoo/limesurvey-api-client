<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Type;

use Meritoo\Common\Test\Base\BaseTypeTestCase;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\ApiClient\Type\SystemMethodType;

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
        static::assertHasNoConstructor(MethodType::className);
    }

    /**
     * @param string $incorrectMethod Type of method to verify
     * @dataProvider provideIncorrectMethod
     */
    public function testGetValidatedMethodWithIncorrectMethod($incorrectMethod)
    {
        $this->setExpectedException(UnknownMethodException::className);
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
        $this->setExpectedException(UnknownMethodException::className);
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
     * @return array
     * //return Generator
     */
    public function provideMethod()
    {
        return [
            [
                MethodType::ADD_RESPONSE,
            ],
            [
                MethodType::EXPORT_STATISTICS,
            ],
            [
                MethodType::GET_PARTICIPANT_PROPERTIES,
            ],
            [
                MethodType::LIST_SURVEYS,
            ],
            [
                SystemMethodType::GET_SESSION_KEY,
            ],
            [
                SystemMethodType::RELEASE_SESSION_KEY,
            ],
        ];

        /*
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

        yield[
            SystemMethodType::GET_SESSION_KEY,
        ];

        yield[
            SystemMethodType::RELEASE_SESSION_KEY,
        ];
        */
    }

    /**
     * Provides incorrect type of method
     *
     * @return array
     * //return Generator
     */
    public function provideIncorrectMethod()
    {
        return [
            [
                '',
            ],
            [
                null,
            ],
            [
                true,
            ],
            [
                false,
            ],
            [
                'lorem',
            ],
        ];

        /*
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
        */
    }

    /**
     * Provides type of method who result provided by the API is iterable and information if it's iterable
     *
     * @return array
     * //return Generator
     */
    public function provideIterableType()
    {
        return [
            [
                MethodType::ADD_RESPONSE,
                false,
            ],
            [
                MethodType::GET_PARTICIPANT_PROPERTIES,
                false,
            ],
            [
                MethodType::LIST_PARTICIPANTS,
                true,
            ],
            [
                MethodType::LIST_QUESTIONS,
                true,
            ],
            [
                MethodType::LIST_SURVEYS,
                true,
            ],
            [
                MethodType::LIST_USERS,
                true,
            ],
        ];

        /*
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
        */
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllExpectedTypes()
    {
        return [
            'ADD_PARTICIPANTS'           => MethodType::ADD_PARTICIPANTS,
            'ADD_RESPONSE'               => MethodType::ADD_RESPONSE,
            'EXPORT_STATISTICS'          => MethodType::EXPORT_STATISTICS,
            'GET_PARTICIPANT_PROPERTIES' => MethodType::GET_PARTICIPANT_PROPERTIES,
            'GET_QUESTION_PROPERTIES'    => MethodType::GET_QUESTION_PROPERTIES,
            'GET_SUMMARY'                => MethodType::GET_SUMMARY,
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
        return [
            [
                '',
                false,
            ],
            [
                'lorem',
                false,
            ],
            [
                MethodType::ADD_RESPONSE,
                true,
            ],
            [
                MethodType::GET_PARTICIPANT_PROPERTIES,
                true,
            ],
        ];

        /*
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
        */
    }
}
