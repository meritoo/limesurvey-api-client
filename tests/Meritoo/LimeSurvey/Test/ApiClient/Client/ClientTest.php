<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Client;

use Generator;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Result;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the client of the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ClientTest extends BaseTestCase
{
    /**
     * Configuration used while connecting to LimeSurvey's API
     *
     * @var ConnectionConfiguration
     */
    private $configuration;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(Client::class, OopVisibilityType::IS_PUBLIC, 3, 1);
    }

    /**
     * @param string $incorrectMethod Incorrect name of method to call
     * @dataProvider provideIncorrectMethod
     */
    public function testRunWithIncorrectMethod($incorrectMethod)
    {
        $this->expectException(UnknownMethodException::class);

        $client = new Client($this->configuration);
        $client->run($incorrectMethod);
    }

    /**
     * @param string $method          Name of method to call
     * @param array  $arguments       Arguments of the method to call
     * @param bool   $debugMode       If is set to true, the "debug" mode is turned on. Otherwise - turned off.
     * @param mixed  $expectedRawData Expected raw data returned by JsonRpcClient
     *
     * @dataProvider provideMethod
     */
    public function testRun($method, $arguments, $debugMode, $expectedRawData)
    {
        $sessionManager = $this->createMock(SessionManager::class);
        $rpcClientManager = $this->createMock(JsonRpcClientManager::class);

        $rpcClientManager
            ->expects(static::any())
            ->method('runMethod')
            ->willReturn($expectedRawData);

        $configuration = new ConnectionConfiguration(
            $this->configuration->getBaseUrl(),
            $this->configuration->getUsername(),
            $this->configuration->getPassword(),
            $debugMode,
            $this->configuration->isVerifySslCertificateOn()
        );

        $client = new Client($configuration, $rpcClientManager, $sessionManager);
        static::assertInstanceOf(Result::class, $client->run($method, $arguments));
    }

    public function testGetConfiguration()
    {
        $client = new Client($this->configuration);
        static::assertEquals($this->configuration, $client->getConfiguration());
    }

    public function testGetRpcClientManagerVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(Client::class, 'getRpcClientManager', OopVisibilityType::IS_PRIVATE);
    }

    public function testGetSessionManagerVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(Client::class, 'getRpcClientManager', OopVisibilityType::IS_PRIVATE);
    }

    /**
     * Provides incorrect name of method
     *
     * @return Generator
     */
    public function provideIncorrectMethod()
    {
        yield[
            'lorem',
        ];

        yield[
            'ipsum',
        ];

        yield[
            '',
        ];
    }

    /**
     * Provides correct name of method
     *
     * @return Generator
     */
    public function provideMethod()
    {
        yield[
            MethodType::GET_PARTICIPANT_PROPERTIES,
            [],
            true,
            [],
        ];

        yield[
            MethodType::LIST_SURVEYS,
            [],
            false,
            [],
        ];

        yield[
            MethodType::LIST_PARTICIPANTS,
            [],
            false,
            null,
        ];

        /*
         * todo: Use/Verify other types of methods
         */
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
