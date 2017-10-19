<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Service;

use Exception;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Surveys;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Service\SurveyService;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test case of the service that serves surveys
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveyServiceTest extends BaseTestCase
{
    /**
     * Service that serves surveys.
     * Without surveys.
     *
     * @var SurveyService
     */
    private $serviceWithoutSurveys;

    /**
     * Service that serves surveys.
     * With surveys.
     *
     * @var SurveyService
     */
    private $serviceWithSurveys;

    /**
     * Base url of LimeSurvey's instance.
     * Used to prepare configuration of connection.
     *
     * @var string
     */
    private $connectionBaseUrl = 'http://test.com';

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(SurveyService::className, OopVisibilityType::IS_PUBLIC, 2, 1);
    }

    public function testGetClient()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->createServiceWithSurveys($rpcClientManager, $sessionManager);

        static::assertInstanceOf(Client::className, $this->serviceWithoutSurveys->getClient());
        static::assertInstanceOf(Client::className, $this->serviceWithSurveys->getClient());

        $connectionConfiguration = $this->getConnectionConfiguration();
        $client = new Client($connectionConfiguration);
        $surveyService = new SurveyService($client);

        static::assertEquals($client, $surveyService->getClient());
    }

    public function testGetAllSurveysWithImportantException()
    {
        $this->setExpectedException(CannotProcessDataException::className);
        $exception = new CannotProcessDataException(ReasonType::NO_TOKEN_TABLE);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->serviceWithoutSurveys->getAllSurveys();
    }

    public function testGetAllSurveysWithNoSurveysException()
    {
        $exception = new CannotProcessDataException(ReasonType::NO_SURVEYS_FOUND);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        static::assertCount(0, $this->serviceWithoutSurveys->getAllSurveys());
    }

    public function testGetAllSurveys()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(2);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->createServiceWithSurveys($rpcClientManager, $sessionManager);

        /*
         * If there are no surveys, count of all or active only surveys is the same
         */
        static::assertCount(0, $this->serviceWithoutSurveys->getAllSurveys());
        static::assertCount(0, $this->serviceWithoutSurveys->getAllSurveys(true));

        /*
         * If there are surveys, here we've got difference between count of all or active only surveys
         */
        static::assertCount(3, $this->serviceWithSurveys->getAllSurveys());
        static::assertCount(2, $this->serviceWithSurveys->getAllSurveys(true));
    }

    public function testIsExistingSurvey()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(4);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->createServiceWithSurveys($rpcClientManager, $sessionManager);

        /*
         * If there are no surveys, verification of existence any survey always return false
         */
        static::assertFalse($this->serviceWithoutSurveys->isExistingSurvey(1));
        static::assertFalse($this->serviceWithoutSurveys->isExistingSurvey(1, true));

        static::assertFalse($this->serviceWithoutSurveys->isExistingSurvey(2));
        static::assertFalse($this->serviceWithoutSurveys->isExistingSurvey(2, true));

        /*
         * If there are surveys, verification of existence active survey always return true
         */
        static::assertTrue($this->serviceWithSurveys->isExistingSurvey(1));
        static::assertTrue($this->serviceWithSurveys->isExistingSurvey(1, true));

        static::assertTrue($this->serviceWithSurveys->isExistingSurvey(2));
        static::assertTrue($this->serviceWithSurveys->isExistingSurvey(2, true));

        /*
         * If there are surveys, verification of existence of non-active survey shows difference
         */
        static::assertTrue($this->serviceWithSurveys->isExistingSurvey(3));
        static::assertFalse($this->serviceWithSurveys->isExistingSurvey(3, true));

        /*
         * If there are surveys, verification of existence non-existing survey always return false
         */
        static::assertFalse($this->serviceWithSurveys->isExistingSurvey(4));
        static::assertFalse($this->serviceWithSurveys->isExistingSurvey(4, true));
    }

    public function testGetStartSurveyUrl()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->createServiceWithSurveys($rpcClientManager, $sessionManager);

        $surveyId = 123;
        $token = uniqid();
        $expectedUrl = sprintf('%s/%d?token=%s', $this->connectionBaseUrl, $surveyId, $token);

        $participant = new Participant([
            'tid'       => 1,
            'firstname' => 'John',
            'lastname'  => 'Scott',
            'email'     => 'john@scott.com',
            'token'     => $token,
        ]);

        static::assertEquals($expectedUrl, $this->serviceWithoutSurveys->getStartSurveyUrl($surveyId, $participant));
        static::assertEquals($expectedUrl, $this->serviceWithSurveys->getStartSurveyUrl($surveyId, $participant));
    }

    /**
     * Returns configuration used while connecting to LimeSurvey's API
     *
     * @return ConnectionConfiguration
     */
    private function getConnectionConfiguration()
    {
        return new ConnectionConfiguration($this->connectionBaseUrl, 'test', 'test');
    }

    /**
     * Returns manager of session started while connecting to LimeSurvey's API
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getSessionManager()
    {
        return $this->createMock(SessionManager::className);
    }

    /**
     * Returns manager of the JsonRPC client used while connecting to LimeSurvey's API with mocked method runMethod()
     *
     * @param int   $runMethodCallCount   Count of calls of the runMethod() method (who is mocked)
     * @param array $runMethodCallResults (optional) Results of calls of the runMethod() method (who is mocked)
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getJsonRpcClientManager($runMethodCallCount, array $runMethodCallResults = [])
    {
        $rpcClientManager = $this->createMock(JsonRpcClientManager::className);

        $rpcClientManager
            ->expects(static::exactly($runMethodCallCount))
            ->method('runMethod')
            ->will(static::returnValue($runMethodCallResults));

        return $rpcClientManager;
    }

    /**
     * Returns manager of the JsonRPC client used while connecting to LimeSurvey's API with mocked method runMethod()
     * that throws an exception
     *
     * @param int       $runMethodCallCount Count of calls of the runMethod() method (who is mocked)
     * @param Exception $exception          The exception that should be thrown
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getJsonRpcClientManagerWithException($runMethodCallCount, Exception $exception)
    {
        $rpcClientManager = $this->createMock(JsonRpcClientManager::className);

        $rpcClientManager
            ->expects(static::exactly($runMethodCallCount))
            ->method('runMethod')
            ->willThrowException($exception);

        return $rpcClientManager;
    }

    /**
     * Creates instance of the tested service without surveys
     *
     * @param PHPUnit_Framework_MockObject_MockObject $rpcClientManager Manager of the JsonRPC client used while connecting to LimeSurvey's API
     * @param PHPUnit_Framework_MockObject_MockObject $sessionManager   Manager of session started while connecting to LimeSurvey's API
     */
    private function createServiceWithoutSurveys(PHPUnit_Framework_MockObject_MockObject $rpcClientManager, PHPUnit_Framework_MockObject_MockObject $sessionManager)
    {
        $configuration = $this->getConnectionConfiguration();
        $client = new Client($configuration, $rpcClientManager, $sessionManager);
        $this->serviceWithoutSurveys = new SurveyService($client);
    }

    /**
     * Creates instance of the tested service with surveys
     *
     * @param PHPUnit_Framework_MockObject_MockObject $rpcClientManager Manager of the JsonRPC client used while connecting to LimeSurvey's API
     * @param PHPUnit_Framework_MockObject_MockObject $sessionManager   Manager of session started while connecting to LimeSurvey's API
     */
    private function createServiceWithSurveys(PHPUnit_Framework_MockObject_MockObject $rpcClientManager, PHPUnit_Framework_MockObject_MockObject $sessionManager)
    {
        $configuration = $this->getConnectionConfiguration();
        $client = new Client($configuration, $rpcClientManager, $sessionManager);

        $allSurveys = new Surveys([
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
        ]);

        $this->serviceWithSurveys = new SurveyService($client, $allSurveys);
    }
}
