<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Manager;

use JsonRPC\Client as RpcClient;
use JsonRPC\Exception\InvalidJsonFormatException;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\InvalidResultOfMethodRunException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\Test\ApiClient\Result\Item\SurveyTest;

/**
 * Test case of the manager of the JsonRPC client used while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class JsonRpcClientManagerTest extends BaseTestCase
{
    /**
     * Configuration used while connecting to LimeSurvey's API
     *
     * @var ConnectionConfiguration
     */
    private $configuration;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(JsonRpcClientManager::class, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    public function testRunMethodWithEmptyArrayReturned()
    {
        $rpcClient = $this->createMock(RpcClient::class);

        $manager = $this
            ->getMockBuilder(JsonRpcClientManager::class)
            ->setConstructorArgs([
                $this->configuration,
            ])
            ->setMethods([
                'getRpcClient',
            ])
            ->getMock();

        $rpcClient
            ->expects(static::once())
            ->method('execute')
            ->willReturn([]);

        $manager
            ->expects(static::once())
            ->method('getRpcClient')
            ->willReturn($rpcClient);

        /* @var JsonRpcClientManager $manager */
        static::assertEquals([], $manager->runMethod(MethodType::LIST_SURVEYS));
    }

    public function testRunMethodWithRawDataReturned()
    {
        $rpcClient = $this->createMock(RpcClient::class);
        $manager = $this->createPartialMock(JsonRpcClientManager::class, ['getRpcClient']);

        $rpcClient
            ->expects(static::once())
            ->method('execute')
            ->willReturn(SurveyTest::getSurveysRawData());

        $manager
            ->expects(static::once())
            ->method('getRpcClient')
            ->willReturn($rpcClient);

        /* @var JsonRpcClientManager $manager */
        static::assertEquals(SurveyTest::getSurveysRawData(), $manager->runMethod(MethodType::LIST_SURVEYS));
    }

    public function testRunMethodWithException()
    {
        $this->expectException(InvalidResultOfMethodRunException::class);

        $manager = $this->createPartialMock(JsonRpcClientManager::class, ['getRpcClient']);
        $rpcClient = $this->createMock(RpcClient::class);

        $rpcClient
            ->expects(self::once())
            ->method('execute')
            ->willThrowException(new InvalidJsonFormatException('bla bla'));

        $manager
            ->expects(static::once())
            ->method('getRpcClient')
            ->willReturn($rpcClient);

        /* @var JsonRpcClientManager $manager */
        $manager->runMethod(MethodType::LIST_SURVEYS);
    }

    public function testGetRpcClientVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(JsonRpcClientManager::class, 'getRpcClient', OopVisibilityType::IS_PROTECTED);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->configuration = new ConnectionConfiguration('http://test.com', 'test', 'test');
    }
}
