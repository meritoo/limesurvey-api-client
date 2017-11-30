<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use Generator;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the one item of the result/data: survey's summary (contains aggregated data)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveySummaryTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(SurveySummary::class, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    /**
     * @param array $rawData Raw data of survey's summary
     * @dataProvider provideRawData
     */
    public function testCreateOfTheSurveySummary(array $rawData)
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::GET_SUMMARY, $rawData);

        /* @var SurveySummary $processed */
        static::assertEquals($rawData['token_count'], $processed->getTokenCount());
        static::assertEquals($rawData['token_invalid'], $processed->getTokenInvalidCount());
        static::assertEquals($rawData['token_sent'], $processed->getTokenSentCount());
        static::assertEquals($rawData['token_opted_out'], $processed->getTokenOptedOutCount());
        static::assertEquals($rawData['token_completed'], $processed->getTokenCompletedCount());
        static::assertEquals($rawData['completed_responses'], $processed->getCompleteResponsesCount());
        static::assertEquals($rawData['incomplete_responses'], $processed->getIncompleteResponsesCount());
        static::assertEquals($rawData['full_responses'], $processed->getFullResponsesCount());
    }

    /**
     * Provides raw data of survey's summary
     *
     * @return Generator
     */
    public function provideRawData()
    {
        yield[
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
        ];

        yield[
            [
                'token_count'          => '28',
                'token_invalid'        => '0',
                'token_sent'           => '5',
                'token_opted_out'      => '0',
                'token_completed'      => '6',
                'completed_responses'  => '6',
                'incomplete_responses' => '10',
                'full_responses'       => '16',
            ],
        ];

        yield[
            [
                'token_count'          => '28',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '2',
                'completed_responses'  => '2',
                'incomplete_responses' => '12',
                'full_responses'       => '14',
            ],
        ];
    }
}
