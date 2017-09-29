<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Generator;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\IncorrectClassOfResultItemException;
use stdClass;

/**
 * Test case of an exception used while class used to create instance of one item of the result is incorrect
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class IncorrectClassOfResultItemExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(IncorrectClassOfResultItemException::class, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    /**
     * @param string $className       Incorrect class name used to create instance of one item
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideIncorrectClassName
     */
    public function testConstructorMessage($className, $expectedMessage)
    {
        $exception = new IncorrectClassOfResultItemException($className);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides incorrect class name used to create instance of one item
     *
     * @return Generator
     */
    public function provideIncorrectClassName()
    {
        $template = 'Class %s used to create instance of one item of the result should extend %s, but it does not. Did'
            . ' you forget to use proper base class?';

        yield[
            stdClass::class,
            sprintf($template, stdClass::class, BaseItem::class),
        ];
    }
}
