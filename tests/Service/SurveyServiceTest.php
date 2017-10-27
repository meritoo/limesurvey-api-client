<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Service;

use Exception;
use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
use Meritoo\LimeSurvey\ApiClient\Exception\MissingSurveySummaryException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Surveys;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Service\SurveyService;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test case of the service that serves surveys and participants of surveys
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveyServiceTest extends BaseTestCase
{
    /**
     * Service that serves surveys and participants of surveys.
     * Without surveys.
     *
     * @var SurveyService
     */
    private $serviceWithoutSurveys;

    /**
     * Service that serves surveys and participants of surveys.
     * With surveys.
     *
     * @var SurveyService
     */
    private $serviceWithSurveys;

    /**
     * Service that serves surveys and participants of surveys.
     * Without participants.
     *
     * @var SurveyService
     */
    private $serviceWithoutParticipants;

    /**
     * Service that serves surveys and participants of surveys.
     * With participants.
     *
     * @var SurveyService
     */
    private $serviceWithParticipants;

    /**
     * Base url of LimeSurvey's instance.
     * Used to prepare configuration of connection.
     *
     * @var string
     */
    private $connectionBaseUrl = 'http://test.com';

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(SurveyService::className, OopVisibilityType::IS_PUBLIC, 4, 1);
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

    public function testGetAllSurveysWithNoTableException()
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

    public function testGetStartSurveyUrlByToken()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);
        $this->createServiceWithSurveys($rpcClientManager, $sessionManager);

        $surveyId = 123;
        $token = uniqid();
        $expectedUrl = sprintf('%s/%d?token=%s', $this->connectionBaseUrl, $surveyId, $token);

        static::assertEquals($expectedUrl, $this->serviceWithoutSurveys->getStartSurveyUrlByToken($surveyId, $token));
        static::assertEquals($expectedUrl, $this->serviceWithSurveys->getStartSurveyUrlByToken($surveyId, $token));
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

    public function testGetSurveyParticipantsWithNotExistingSurveyException()
    {
        $exception = new CannotProcessDataException(ReasonType::NOT_EXISTING_SURVEY_ID);
        $this->setExpectedException(CannotProcessDataException::className, $exception->getMessage());

        $runMethodCallResults = [
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
            [
                'status' => ReasonType::NOT_EXISTING_SURVEY_ID,
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(2, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithParticipants->getSurveyParticipants(3);
    }

    public function testGetSurveyParticipantsWithNoParticipantsFoundException()
    {
        $runMethodCallResults = [
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
            [
                'status' => ReasonType::NO_PARTICIPANTS_FOUND,
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(2, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);
        $participants = $this->serviceWithParticipants->getSurveyParticipants(3);

        static::assertInstanceOf(Collection::className, $participants);
        static::assertCount(0, $participants);
    }

    public function testGetSurveyParticipants()
    {
        $runMethodCallResults = [
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
            null,
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
            null,
            [
                'token_count'          => '2',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(6, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertCount(0, $this->serviceWithoutParticipants->getSurveyParticipants(1));
        static::assertCount(0, $this->serviceWithoutParticipants->getSurveyParticipants(2));

        static::assertCount(2, $this->serviceWithParticipants->getSurveyParticipants(1));
        static::assertCount(1, $this->serviceWithParticipants->getSurveyParticipants(2));
        static::assertCount(0, $this->serviceWithParticipants->getSurveyParticipants(3));
    }

    public function testGetSurveyParticipantsWithNoTableException()
    {
        $this->setExpectedException(CannotProcessDataException::className);
        $exception = new CannotProcessDataException(ReasonType::NO_TOKEN_TABLE);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);
        $this->serviceWithParticipants->getSurveyParticipants(3);
    }

    public function testGetSurveyParticipantsWithNoParticipantsException()
    {
        $this->setExpectedException(CannotProcessDataException::className);
        $exception = new CannotProcessDataException(ReasonType::NO_PARTICIPANTS_FOUND);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);
        static::assertCount(0, $this->serviceWithParticipants->getSurveyParticipants(3));
    }

    public function testAddParticipantForNotExistingSurvey()
    {
        $this->setExpectedException(CannotProcessDataException::className);
        $exception = new CannotProcessDataException(ReasonType::NOT_EXISTING_SURVEY_ID);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $surveyId = 1;
        $firstName = 'John';
        $lastName = 'Scott';
        $email = 'john@scott.com';

        $this->serviceWithoutParticipants->addParticipant($surveyId, $firstName, $lastName, $email);
        $this->serviceWithParticipants->addParticipant($surveyId, $firstName, $lastName, $email);
    }

    public function testAddParticipant()
    {
        $surveyId = 1;
        $firstName = 'John';
        $lastName = 'Scott';
        $email = 'john@scott.com';
        $runMethodCallCount = 1;

        $runMethodCallResults = [
            [
                [
                    'firstname' => $firstName,
                    'lastname'  => $lastName,
                    'email'     => $email,
                ],
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager($runMethodCallCount, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $result = $this->serviceWithoutParticipants->addParticipant($surveyId, $firstName, $lastName, $email);

        static::assertInstanceOf(Participant::className, $result);
        static::assertEquals($firstName, $result->getFirstName());
        static::assertEquals($lastName, $result->getLastName());
        static::assertEquals($email, $result->getEmail());
    }

    public function testGetParticipant()
    {
        $runMethodCallResults = [
            [
                'token_count'          => '0',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
            null,
            [
                [
                    'tid'              => 1,
                    'participant_info' => [
                        'firstname' => 'John',
                        'lastname'  => 'Scott',
                        'email'     => 'john@scott.com',
                    ],
                ],
                [
                    'tid'              => 2,
                    'participant_info' => [
                        'firstname' => 'Mary',
                        'lastname'  => 'Jane',
                        'email'     => 'mary@jane.com',
                    ],
                ],
            ],
            [
                'token_count'          => '2',
                'token_invalid'        => '0',
                'token_sent'           => '0',
                'token_opted_out'      => '0',
                'token_completed'      => '0',
                'completed_responses'  => '0',
                'incomplete_responses' => '0',
                'full_responses'       => '0',
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(2, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $participant1 = $this->serviceWithoutParticipants->getParticipant(1, 'john@scott.com');
        $participant2 = $this->serviceWithParticipants->getParticipant(1, 'john@scott.com');

        static::assertNull($participant1);
        static::assertInstanceOf(ParticipantShort::className, $participant2);
        static::assertEquals('John', $participant2->getFirstName());
        static::assertEquals('Scott', $participant2->getLastName());
        static::assertEquals('john@scott.com', $participant2->getEmail());
    }

    public function testGetSurveyTokenCountWithException()
    {
        $this->setExpectedException(MissingSurveySummaryException::className);

        $runMethodCallResults = [
            null,
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(1, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithoutSurveys($rpcClientManager, $sessionManager);

        $this->serviceWithoutSurveys->getSurveyTokenCount(1);
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

        $mocker = $rpcClientManager
            ->expects(static::exactly($runMethodCallCount))
            ->method('runMethod');

        if (!empty($runMethodCallResults)) {
            $function = [
                $mocker,
                'willReturnOnConsecutiveCalls',
            ];

            /*
             * I have to use the call_user_func_array() function to pass elements of $runMethodCallResults array as
             * arguments of the willReturnOnConsecutiveCalls() method
             */
            call_user_func_array($function, $runMethodCallResults);
        }

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

    /**
     * Creates instance of the tested service without participants
     *
     * @param PHPUnit_Framework_MockObject_MockObject $rpcClientManager Manager of the JsonRPC client used while connecting to LimeSurvey's API
     * @param PHPUnit_Framework_MockObject_MockObject $sessionManager   Manager of session started while connecting to LimeSurvey's API
     */
    private function createServiceWithoutParticipants(PHPUnit_Framework_MockObject_MockObject $rpcClientManager, PHPUnit_Framework_MockObject_MockObject $sessionManager)
    {
        $configuration = $this->getConnectionConfiguration();
        $client = new Client($configuration, $rpcClientManager, $sessionManager);
        $this->serviceWithoutParticipants = new SurveyService($client);
    }

    /**
     * Creates instance of the tested service with participants
     *
     * @param PHPUnit_Framework_MockObject_MockObject $rpcClientManager Manager of the JsonRPC client used while connecting to LimeSurvey's API
     * @param PHPUnit_Framework_MockObject_MockObject $sessionManager   Manager of session started while connecting to LimeSurvey's API
     */
    private function createServiceWithParticipants(PHPUnit_Framework_MockObject_MockObject $rpcClientManager, PHPUnit_Framework_MockObject_MockObject $sessionManager)
    {
        $configuration = $this->getConnectionConfiguration();
        $client = new Client($configuration, $rpcClientManager, $sessionManager);

        $allParticipants = new Participants([
            1 => new Collection([
                new ParticipantShort([
                    'tid'              => 1,
                    'participant_info' => [
                        'firstname' => 'John',
                        'lastname'  => 'Scott',
                        'email'     => 'john@scott.com',
                    ],
                ]),
                new ParticipantShort([
                    'tid'              => 2,
                    'participant_info' => [
                        'firstname' => 'Mary',
                        'lastname'  => 'Jane',
                        'email'     => 'mary@jane.com',
                    ],
                ]),
            ]),
            2 => new Collection([
                new ParticipantShort(),
            ]),
        ]);

        $this->serviceWithParticipants = new SurveyService($client, null, $allParticipants);
    }
}
