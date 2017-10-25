<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Result;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
use Meritoo\LimeSurvey\ApiClient\Result\Result;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\Test\ApiClient\Utilities\DateUtility;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test case of the result with data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ResultTest extends BaseTestCase
{
    /**
     * Empty data returned by the LimeSurvey's API
     *
     * @var array
     */
    private $emptyData;

    /**
     * Iterable, not empty data returned by the LimeSurvey's API
     *
     * @var array
     */
    private $iterableData;

    /**
     * Not iterable, not empty data returned by the LimeSurvey's API
     *
     * @var array
     */
    private $notIterableData;

    /**
     * Status provided instead of real data.
     * An array with one key: "status".
     *
     * @var array
     */
    private $statusInsteadData;

    /**
     * Result with empty data returned by the LimeSurvey's API.
     * Mock of the tested class.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $emptyDataResult;

    /**
     * Result with iterable, not empty data.
     * Mock of the tested class.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $iterableDataResult;

    /**
     * Result with not iterable, not empty data.
     * Mock of the tested class.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $notIterableDataResult;

    /**
     * Result with status provided instead of real data
     *
     * @var Result
     */
    private $statusInsteadDataResult;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(Result::class, OopVisibilityType::IS_PUBLIC, 2, 2);
    }

    public function testIsEmpty()
    {
        static::assertTrue($this->emptyDataResult->isEmpty());
        static::assertFalse($this->iterableDataResult->isEmpty());
    }

    public function testGetDataUsingProcessedData()
    {
        $emptyData = $this->emptyDataResult->getData();
        $iterableData = $this->iterableDataResult->getData();
        $notIterableData = $this->notIterableDataResult->getData();

        static::assertEmpty($emptyData);
        static::assertNotEmpty($iterableData);
        static::assertNotEmpty($notIterableData);

        static::assertCount(count($this->emptyData), $emptyData);
        static::assertCount(count($this->iterableData), $iterableData);
        static::assertInstanceOf(BaseItem::class, $notIterableData);
    }

    public function testGetDataUsingRawData()
    {
        $emptyData = $this->emptyDataResult->getData(true);
        $iterableData = $this->iterableDataResult->getData(true);

        static::assertEmpty($emptyData);
        static::assertNotEmpty($iterableData);

        static::assertCount(count($this->emptyData), $emptyData);
        static::assertCount(count($this->iterableData), $iterableData);

        static::assertEquals($this->emptyData, $emptyData);
        static::assertEquals($this->iterableData, $iterableData);
    }

    public function testGetDataUsingProcessedDataWhoCannotBeProcessed()
    {
        $this->expectException(CannotProcessDataException::class);
        $this->statusInsteadDataResult->getData();
    }

    public function testGetProcessedDataVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(Result::class, 'getProcessedData', OopVisibilityType::IS_PRIVATE, 1, 1);
    }

    public function testGetResultProcessorVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(Result::class, 'getResultProcessor', OopVisibilityType::IS_PRIVATE);
    }

    public function testGetStatusWhenIsNotProvided()
    {
        $result = new Result(MethodType::ADD_PARTICIPANTS, []);

        static::assertEquals(null, $result->getStatus());
        static::assertEquals([], $result->getData(true));
    }

    public function testGetStatusWhenIsProvided()
    {
        static::assertEquals($this->statusInsteadData['status'], $this->statusInsteadDataResult->getStatus());
        static::assertEquals([], $this->statusInsteadDataResult->getData(true));
    }

    /**
     * {@inheritdoc{
     */
    protected function setUp()
    {
        parent::setUp();
        $this->emptyData = [];

        $this->notIterableData = [
            'result' => base64_encode('lorem-ipsum'),
        ];

        $this->iterableData = [
            [
                'sid'            => '123',
                'surveyls_title' => 'Test',
                'startdate'      => null,
                'expires'        => null,
                'active'         => 'N',
            ],
            [
                'sid'            => '456',
                'surveyls_title' => 'Another Test',
                'startdate'      => DateUtility::getDateTime(),
                'expires'        => null,
                'active'         => 'Y',
            ],
        ];

        $this->statusInsteadData = [
            'status' => 'Invalid data',
        ];

        $emptyData = [
            MethodType::LIST_SURVEYS,
            $this->emptyData,
        ];

        $iterableData = [
            MethodType::LIST_SURVEYS,
            $this->iterableData,
        ];

        $notIterableData = [
            MethodType::GET_PARTICIPANT_PROPERTIES,
            $this->notIterableData,
        ];

        $this->emptyDataResult = $this->getResultMock($emptyData);
        $this->iterableDataResult = $this->getResultMock($iterableData);
        $this->notIterableDataResult = $this->getResultMock($notIterableData);
        $this->statusInsteadDataResult = new Result(MethodType::LIST_PARTICIPANTS, $this->statusInsteadData);
    }

    /**
     * Returns mock of the tested class
     *
     * @param array $constructorArguments Arguments of constructor for prepared mock
     * @return Result
     */
    private function getResultMock($constructorArguments)
    {
        return $this->getMockForAbstractClass(Result::class, $constructorArguments);
    }
}
