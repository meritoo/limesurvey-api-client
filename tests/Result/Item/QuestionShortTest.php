<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Item\QuestionShort;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the one item of the result/data: short data of one question of survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class QuestionShortTest extends BaseTestCase
{
    /**
     * Raw data of questions
     *
     * @var array
     */
    private $rawData;

    /**
     * 1st instance of the question created using the raw data
     *
     * @var QuestionShort
     */
    private $question1stInstance;

    /**
     * 2nd instance of the question created using the raw data
     *
     * @var QuestionShort
     */
    private $question2ndInstance;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(QuestionShort::class, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testCreateOfTheQuestionShort()
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::LIST_QUESTIONS, $this->rawData);

        static::assertCount(2, $processed);
    }

    public function testGetId()
    {
        static::assertEquals(123, $this->question1stInstance->getId());
        static::assertEquals(456, $this->question2ndInstance->getId());
    }

    public function testGetParentId()
    {
        static::assertEquals(0, $this->question1stInstance->getParentId());
        static::assertEquals(789, $this->question2ndInstance->getParentId());
    }

    public function testGetSurveyId()
    {
        static::assertEquals(0, $this->question1stInstance->getSurveyId());
        static::assertEquals(1020, $this->question2ndInstance->getSurveyId());
    }

    public function testGetGroupId()
    {
        static::assertEquals(0, $this->question1stInstance->getGroupId());
        static::assertEquals(3040, $this->question2ndInstance->getGroupId());
    }

    public function testGetScaleId()
    {
        static::assertEquals(0, $this->question1stInstance->getScaleId());
        static::assertEquals(5060, $this->question2ndInstance->getScaleId());
    }

    public function testGetType()
    {
        static::assertEquals('T', $this->question1stInstance->getType());
        static::assertEquals('N', $this->question2ndInstance->getType());
    }

    public function testGetTitle()
    {
        static::assertEquals('Test', $this->question1stInstance->getTitle());
        static::assertEquals('Another Test', $this->question2ndInstance->getTitle());
    }

    public function testGetContent()
    {
        static::assertEquals('Donec ullamcorper nulla non metus auctor fringilla?', $this->question1stInstance->getContent());
        static::assertEquals('Maecenas sed diam eget risus varius blandit sit amet non magna?', $this->question2ndInstance->getContent());
    }

    public function testGetContentHelp()
    {
        static::assertEquals('Maecenas sed diam eget risus varius blandit sit amet non magna.', $this->question1stInstance->getContentHelp());
        static::assertEquals('Donec id elit non mi porta gravida at eget metus.', $this->question2ndInstance->getContentHelp());
    }

    public function testGetRegularExpression()
    {
        static::assertNull($this->question1stInstance->getRegularExpression());
        static::assertEquals('\d+', $this->question2ndInstance->getRegularExpression());
    }

    public function testIsOther()
    {
        static::assertFalse($this->question1stInstance->isOther());
        static::assertFalse($this->question2ndInstance->isOther());
    }

    public function testIsMandatory()
    {
        static::assertTrue($this->question1stInstance->isMandatory());
        static::assertTrue($this->question2ndInstance->isMandatory());
    }

    public function testGetPosition()
    {
        static::assertEquals(1, $this->question1stInstance->getPosition());
        static::assertEquals(2, $this->question2ndInstance->getPosition());
    }

    public function testGetLanguage()
    {
        static::assertEquals('pl', $this->question1stInstance->getLanguage());
        static::assertEquals('pl', $this->question2ndInstance->getLanguage());
    }

    public function testGetSameDefault()
    {
        static::assertEquals(0, $this->question1stInstance->getSameDefault());
        static::assertEquals(0, $this->question2ndInstance->getSameDefault());
    }

    public function testGetRelevance()
    {
        static::assertEquals('', $this->question1stInstance->getRelevance());
        static::assertEquals('1', $this->question2ndInstance->getRelevance());
    }

    public function testGetModuleName()
    {
        static::assertNull($this->question1stInstance->getModuleName());
        static::assertEquals('HR', $this->question2ndInstance->getModuleName());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->rawData = static::getQuestionsRawData();

        $this->question1stInstance = new QuestionShort($this->rawData[0]);
        $this->question2ndInstance = new QuestionShort($this->rawData[1]);
    }

    /**
     * Returns raw data of questions
     *
     * @return array
     */
    private static function getQuestionsRawData()
    {
        return [
            [
                'id'             => [
                    'qid'      => '123',
                    'language' => 'pl',
                ],
                'qid'            => '123',
                'parent_qid'     => null,
                'sid'            => null,
                'gid'            => null,
                'scale_id'       => null,
                'type'           => 'T',
                'title'          => 'Test',
                'question'       => 'Donec ullamcorper nulla non metus auctor fringilla?',
                'help'           => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
                'preg'           => null,
                'other'          => 'N',
                'mandatory'      => 'Y',
                'question_order' => '1',
                'language'       => 'pl',
                'same_default'   => '0',
                'relevance'      => null,
                'modulename'     => null,
            ],
            [
                'id'             => [
                    'qid'      => '456',
                    'language' => 'pl',
                ],
                'qid'            => '456',
                'parent_qid'     => '789',
                'sid'            => '1020',
                'gid'            => '3040',
                'scale_id'       => '5060',
                'type'           => 'N',
                'title'          => 'Another Test',
                'question'       => 'Maecenas sed diam eget risus varius blandit sit amet non magna?',
                'help'           => 'Donec id elit non mi porta gravida at eget metus.',
                'preg'           => '\d+',
                'other'          => 'N',
                'mandatory'      => 'Y',
                'question_order' => '2',
                'language'       => 'pl',
                'same_default'   => '0',
                'relevance'      => '1',
                'modulename'     => 'HR',
            ],
        ];
    }
}
