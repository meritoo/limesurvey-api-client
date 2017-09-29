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
use Meritoo\LimeSurvey\ApiClient\Exception\CreateSessionKeyFailedException;

/**
 * Test case of an exception used while create of the session key has failed
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class CreateSessionKeyFailedExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(CreateSessionKeyFailedException::class, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    /**
     * @param string $reason          Reason of failure, e.g. "Invalid user name or password"
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideReason
     */
    public function testConstructorMessage($reason, $expectedMessage)
    {
        $exception = new CreateSessionKeyFailedException($reason);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides reason of failure
     *
     * @return Generator
     */
    public function provideReason()
    {
        $shortMessage = 'Create of the session key has failed';
        $longMessageTemplate = sprintf('%s. Reason: \'%s\'.', $shortMessage, '%s');

        yield[
            '',
            $shortMessage,
        ];

        yield[
            'Invalid user name or password',
            sprintf($longMessageTemplate, 'Invalid user name or password'),
        ];
    }
}
