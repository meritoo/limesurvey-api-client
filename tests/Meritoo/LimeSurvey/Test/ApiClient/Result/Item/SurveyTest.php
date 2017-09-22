<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use DateTime;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the one item of the result/data: survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveyTest extends BaseTestCase
{
    /**
     * Raw data of surveys
     *
     * @var array
     */
    private $rawData;

    /**
     * 1st instance of the survey created using the raw data
     *
     * @var Survey
     */
    private $survey1stInstance;

    /**
     * 2nd instance of the survey created using the raw data
     *
     * @var Survey
     */
    private $survey2ndInstance;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertHasNoConstructor(Survey::class);
    }

    public function testCreateOfTheSurvey()
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::LIST_SURVEYS, $this->rawData);

        static::assertCount(2, $processed);
    }

    public function testGetId()
    {
        static::assertEquals(123, $this->survey1stInstance->getId());
        static::assertEquals(456, $this->survey2ndInstance->getId());
    }

    public function testGetTitle()
    {
        static::assertEquals('Test', $this->survey1stInstance->getTitle());
        static::assertEquals('Another Test', $this->survey2ndInstance->getTitle());
    }

    public function testGetStartsAt()
    {
        static::assertNull($this->survey1stInstance->getStartsAt());
        static::assertEquals(new DateTime($this->rawData[1]['startdate']), $this->survey2ndInstance->getStartsAt());
    }

    public function testGetExpiresAt()
    {
        static::assertEquals(new DateTime($this->rawData[0]['expires']), $this->survey1stInstance->getExpiresAt());
        static::assertNull($this->survey2ndInstance->getExpiresAt());
    }

    public function testIsActive()
    {
        static::assertFalse($this->survey1stInstance->isActive());
        static::assertTrue($this->survey2ndInstance->isActive());
    }

    /**
     * Returns raw data of surveys
     *
     * @return array
     */
    public static function getSurveysRawData()
    {
        return [
            [
                'sid'            => '123',
                'surveyls_title' => 'Test',
                'startdate'      => null,
                'expires'        => (new DateTime())->format('Y-m-d H:i:s'),
                'active'         => 'N',
            ],
            [
                'sid'            => '456',
                'surveyls_title' => 'Another Test',
                'startdate'      => (new DateTime())->format('Y-m-d H:i:s'),
                'expires'        => null,
                'active'         => 'Y',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->rawData = static::getSurveysRawData();

        $this->survey1stInstance = (new Survey())->setValues($this->rawData[0]);
        $this->survey2ndInstance = (new Survey())->setValues($this->rawData[1]);
    }
}
