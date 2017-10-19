<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Collection;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Surveys;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;

/**
 * Test case of the collection of surveys (the Survey class instances)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveysTest extends BaseTestCase
{
    /**
     * An empty collection of surveys
     *
     * @var Surveys
     */
    private $surveysEmpty;

    /**
     * Not empty collection of surveys
     *
     * @var Surveys
     */
    private $surveysNotEmpty;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(Surveys::className, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testAddWithoutIndex()
    {
        $survey1 = new Survey([
            'sid'            => 3,
            'surveyls_title' => 'Test Test Test',
        ]);

        $survey2 = new Survey([
            'sid'            => 4,
            'surveyls_title' => 'Another Test Test Test',
        ]);

        $this
            ->surveysEmpty
            ->add($survey1)
            ->add($survey2);

        $this
            ->surveysNotEmpty
            ->add($survey1)
            ->add($survey2);

        static::assertEquals($survey1, $this->surveysEmpty[3]);
        static::assertEquals($survey2, $this->surveysEmpty[4]);

        static::assertEquals($survey1, $this->surveysNotEmpty[3]);
        static::assertEquals($survey2, $this->surveysNotEmpty[4]);
    }

    public function testGetAll()
    {
        static::assertCount(0, $this->surveysEmpty->getAll());
        static::assertCount(0, $this->surveysEmpty->getAll(true));

        static::assertCount(3, $this->surveysNotEmpty->getAll());
        static::assertCount(2, $this->surveysNotEmpty->getAll(true));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $surveys = [
            new Survey([
                'sid'            => 1,
                'surveyls_title' => 'Test',
                'active'         => 'Y',
            ]),
            new Survey([
                'sid'            => 2,
                'surveyls_title' => 'Another Test',
                'active'         => 'Y',
            ]),
            new Survey([
                'sid'            => 3,
                'surveyls_title' => 'I am inactive',
            ]),
        ];

        $this->surveysEmpty = new Surveys();
        $this->surveysNotEmpty = new Surveys($surveys);
    }
}
