<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Result;

use DateTime;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Result\Result;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
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
     * Mock of the tested class.
     * With empty data returned by the LimeSurvey's API.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $emptyDataMock;

    /**
     * Mock of the tested class.
     * With iterable, not empty data.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $iterableDataMock;

    /**
     * Mock of the tested class.
     * With not iterable, not empty data.
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $notIterableDataMock;

    public function testConstructorVisibilityAndArguments()
    {
        $this->verifyConstructorVisibilityAndArguments(Result::class, OopVisibilityType::IS_PUBLIC, 2, 2);
    }

    public function testIsEmpty()
    {
        static::assertTrue($this->emptyDataMock->isEmpty());
        static::assertFalse($this->iterableDataMock->isEmpty());
    }

    public function testGetDataUsingProcessedData()
    {
        $emptyData = $this->emptyDataMock->getData();
        $iterableData = $this->iterableDataMock->getData();
        $notIterableData = $this->notIterableDataMock->getData();

        static::assertEmpty($emptyData);
        static::assertNotEmpty($iterableData);
        static::assertNotEmpty($notIterableData);

        static::assertCount(count($this->emptyData), $emptyData);
        static::assertCount(count($this->iterableData), $iterableData);
        static::assertInstanceOf(BaseItem::class, $notIterableData);
    }

    public function testGetDataUsingRawData()
    {
        $emptyData = $this->emptyDataMock->getData(true);
        $iterableData = $this->iterableDataMock->getData(true);

        static::assertEmpty($emptyData);
        static::assertNotEmpty($iterableData);

        static::assertCount(count($this->emptyData), $emptyData);
        static::assertCount(count($this->iterableData), $iterableData);

        static::assertEquals($this->emptyData, $emptyData);
        static::assertEquals($this->iterableData, $iterableData);
    }

    public function testGetProcessedDataVisibilityAndArguments()
    {
        $this->verifyMethodVisibilityAndArguments(Result::class, 'getProcessedData', OopVisibilityType::IS_PRIVATE, 1, 1);
    }

    public function testGetResultProcessorVisibilityAndArguments()
    {
        $this->verifyMethodVisibilityAndArguments(Result::class, 'getResultProcessor', OopVisibilityType::IS_PRIVATE);
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
                'startdate'      => (new DateTime())->format('Y-m-d H:i:s'),
                'expires'        => null,
                'active'         => 'Y',
            ],
        ];

        $emptyDataArguments = [
            MethodType::LIST_SURVEYS,
            $this->emptyData,
        ];

        $iterableDataArguments = [
            MethodType::LIST_SURVEYS,
            $this->iterableData,
        ];

        $notIterableDataArguments = [
            MethodType::GET_PARTICIPANT_PROPERTIES,
            $this->notIterableData,
        ];

        $this->emptyDataMock = $this->getResultMock($emptyDataArguments);
        $this->iterableDataMock = $this->getResultMock($iterableDataArguments);
        $this->notIterableDataMock = $this->getResultMock($notIterableDataArguments);
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
