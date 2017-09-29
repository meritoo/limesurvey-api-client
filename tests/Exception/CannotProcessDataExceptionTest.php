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
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;

/**
 * Test case of an exception used while raw data returned by the LimeSurvey's API cannot be processed
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class CannotProcessDataExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(CannotProcessDataException::class, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    /**
     * @param string $reason          Reason why data cannot be processed, e.g. "Invalid user name or password"
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideReason
     */
    public function testConstructorMessage($reason, $expectedMessage)
    {
        $exception = new CannotProcessDataException($reason);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides reason why data cannot be processed
     *
     * @return Generator
     */
    public function provideReason()
    {
        $template = 'Raw data returned by the LimeSurvey\'s API cannot be processed. Reason: \'%s\'.';

        yield[
            'unknown',
            sprintf($template, 'unknown'),
        ];

        yield[
            'Invalid user name or password',
            sprintf($template, 'Invalid user name or password'),
        ];
    }
}
