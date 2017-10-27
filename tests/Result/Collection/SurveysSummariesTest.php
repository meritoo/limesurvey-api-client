<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Collection;

use Meritoo\Common\Exception\Method\DisabledMethodException;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\SurveysSummaries;
use Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary;

/**
 * Test case of the collection of surveys' summaries (the SurveySummary class instances)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveysSummariesTest extends BaseTestCase
{
    /**
     * Empty collection of surveys' summaries
     *
     * @var SurveysSummaries
     */
    private $emptySurveysSummaries;

    /**
     * Non-empty collection of surveys' summaries
     *
     * @var SurveysSummaries
     */
    private $nonEmptySurveysSummaries;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(SurveysSummaries::className, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testAdd()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new SurveysSummaries())->add('');
    }

    public function testAddMultiple()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new SurveysSummaries())->addMultiple([]);
    }

    public function testHas()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new SurveysSummaries())->has(new SurveySummary());
    }

    /**
     * @param array $summaries Surveys' summaries to add
     * @dataProvider provideSurveysSummaries
     */
    public function testAddSurveysSummaries(array $summaries)
    {
        $existingSummariesCount = $this->nonEmptySurveysSummaries->count();

        $this->emptySurveysSummaries->addSurveysSummaries($summaries);
        $this->nonEmptySurveysSummaries->addSurveysSummaries($summaries);

        static::assertCount(count($summaries), $this->emptySurveysSummaries);
        static::assertCount(count($summaries) + $existingSummariesCount, $this->nonEmptySurveysSummaries);
    }

    public function testHasSurveySummaryUsingNonExistingSurvey()
    {
        static::assertFalse($this->emptySurveysSummaries->hasSurveySummary(1));
        static::assertFalse($this->emptySurveysSummaries->hasSurveySummary(2));

        static::assertFalse($this->nonEmptySurveysSummaries->hasSurveySummary(3));
        static::assertFalse($this->nonEmptySurveysSummaries->hasSurveySummary(4));
    }

    public function testHasSurveySummaryUsingExistingSurvey()
    {
        static::assertTrue($this->nonEmptySurveysSummaries->hasSurveySummary(1));
        static::assertTrue($this->nonEmptySurveysSummaries->hasSurveySummary(2));
    }

    public function testGetSurveySummaryUsingNonExistingSurvey()
    {
        static::assertNull($this->emptySurveysSummaries->getSurveySummary(1));
        static::assertNull($this->emptySurveysSummaries->getSurveySummary(2));

        static::assertNull($this->nonEmptySurveysSummaries->getSurveySummary(3));
        static::assertNull($this->nonEmptySurveysSummaries->getSurveySummary(4));
    }

    public function testGetSurveySummaryUsingExistingSurvey()
    {
        $surveySummary1 = $this->nonEmptySurveysSummaries->getSurveySummary(1);
        $surveySummary2 = $this->nonEmptySurveysSummaries->getSurveySummary(2);

        static::assertInstanceOf(SurveySummary::className, $surveySummary1);
        static::assertInstanceOf(SurveySummary::className, $surveySummary2);

        static::assertEquals(0, $surveySummary1->getTokenCount());
        static::assertEquals(5, $surveySummary2->getTokenCount());

        static::assertEquals(0, $surveySummary1->getFullResponsesCount());
        static::assertEquals(3, $surveySummary2->getFullResponsesCount());
    }

    /**
     * Provides surveys' summaries
     *
     * @return array
     * //return Generator
     */
    public function provideSurveysSummaries()
    {
        return [
            [
                [],
            ],
            [
                [
                    123 => new SurveySummary(),
                ],
            ],
            [
                [
                    100 => new SurveySummary(),
                    500 => new SurveySummary(),
                    800 => new SurveySummary(),
                ],
            ],
        ];

        /*
        yield[
            [],
        ];

        yield[
            [
                123 => new SurveySummary(),
            ],
        ];

        yield[
            [
                100 => new SurveySummary(),
                500 => new SurveySummary(),
                800 => new SurveySummary(),
            ],
        ];
        */
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->emptySurveysSummaries = new SurveysSummaries();

        $this->nonEmptySurveysSummaries = new SurveysSummaries([
            1 => new SurveySummary([
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ]),
            2 => new SurveySummary([
                'token_count'          => '5',
                'token_invalid'        => '2',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '2',
                'completed_responses'  => '1',
                'incomplete_responses' => '2',
                'full_responses'       => '3',
            ]),
        ]);
    }
}
