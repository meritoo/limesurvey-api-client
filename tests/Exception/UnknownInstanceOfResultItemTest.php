<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of an exception used while instance of one item used by result, with data fetched from the LimeSurvey's
 * API, is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class UnknownInstanceOfResultItemTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(UnknownInstanceOfResultItem::className, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    /**
     * @param string $method          Name of called method while talking to the LimeSurvey's API. One of the
     *                                MethodType class constants.
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideMethodName
     */
    public function testConstructorMessage($method, $expectedMessage)
    {
        $exception = new UnknownInstanceOfResultItem($method);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides name of called method
     *
     * @return array
     * //return Generator
     */
    public function provideMethodName()
    {
        $template = 'Class name used to create instance of one item used by result the of \'%s\' LimeSurvey API\'s'
            . ' method is unknown. Proper class is not mapped in %s::%s() method. Did you forget about this?';

        return [
            [
                MethodType::LIST_SURVEYS,
                sprintf($template, MethodType::LIST_SURVEYS, 'Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor', 'getItemClassName'),
            ],
            [
                MethodType::ADD_PARTICIPANTS,
                sprintf($template, MethodType::ADD_PARTICIPANTS, 'Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor', 'getItemClassName'),
            ],
        ];

        /*
        yield[
            MethodType::LIST_SURVEYS,
            sprintf($template, MethodType::LIST_SURVEYS, ResultProcessor::class, 'getItemClassName'),
        ];

        yield[
            MethodType::ADD_PARTICIPANTS,
            sprintf($template, MethodType::ADD_PARTICIPANTS, ResultProcessor::class, 'getItemClassName'),
        ];
        */
    }
}
