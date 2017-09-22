<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Processor;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\Test\ApiClient\Result\Item\SurveyTest;

/**
 * Test case of the processor of the raw data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ResultProcessorTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertHasNoConstructor(ResultProcessor::class);
    }

    public function testProcessWithEmptyRawData()
    {
        $rawData = [];
        $processor = new ResultProcessor();

        static::assertNull($processor->process(MethodType::LIST_SURVEYS, $rawData));
    }

    public function testProcessWithIterableData()
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::LIST_SURVEYS, SurveyTest::getSurveysRawData());

        static::assertNotEmpty($processed);
        static::assertTrue(is_array($processed));
        static::assertCount(2, $processed);
    }

    public function testProcessWithNotIterableData()
    {
        $rawData = [
            'lorem' => 'ipsum',
            'dolor' => 'sit',
        ];

        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::GET_PARTICIPANT_PROPERTIES, $rawData);

        static::assertNotEmpty($processed);
        static::assertFalse(is_array($processed));
        static::assertInstanceOf(BaseItem::class, $processed);
    }

    public function testGetItemInstanceVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(ResultProcessor::class, 'getItemInstance', OopVisibilityType::IS_PRIVATE, 1, 1);
    }

    public function testRunWithUnknownResultClass()
    {
        $this->expectException(UnknownInstanceOfResultItem::class);

        $rawData = [
            'lorem' => 'ipsum',
            'dolor' => 'sit',
        ];

        $processor = new ResultProcessor();
        $processor->process(MethodType::LIST_USERS, $rawData);
    }
}
