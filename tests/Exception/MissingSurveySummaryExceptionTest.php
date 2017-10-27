<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\MissingSurveySummaryException;

/**
 * Test case of an exception used when survey's summary is missing
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MissingSurveySummaryExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(MissingSurveySummaryException::className, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    /**
     * @param int    $surveyId        ID of survey
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideSurveyId
     */
    public function testConstructorMessage($surveyId, $expectedMessage)
    {
        $exception = new MissingSurveySummaryException($surveyId);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * Provides ID of survey
     *
     * @return array
     * //return Generator
     */
    public function provideSurveyId()
    {
        $template = 'Summary of survey with ID %d is missing. Does the survey exist?';

        return [
            [
                1,
                sprintf($template, 1),
            ],
            [
                '123',
                sprintf($template, '123'),
            ],
        ];

        /*
        yield[
            1,
            sprintf($template, 1),
        ];

        yield[
            '123',
            sprintf($template, '123'),
        ];
        */
    }
}
