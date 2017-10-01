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
use Meritoo\LimeSurvey\ApiClient\Exception\MissingParticipantOfSurveyException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Service\ParticipantService;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test case of the service that serves participants
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantServiceTest extends BaseTestCase
{
    /**
     * Service that serves participants.
     * Without participants.
     *
     * @var ParticipantService
     */
    private $serviceWithoutParticipants;

    /**
     * Service that serves participants.
     * With participants.
     *
     * @var ParticipantService
     */
    private $serviceWithParticipants;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(ParticipantService::class, OopVisibilityType::IS_PUBLIC, 2, 1);
    }

    public function testGetClient()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertInstanceOf(Client::class, $this->serviceWithoutParticipants->getClient());
        static::assertInstanceOf(Client::class, $this->serviceWithParticipants->getClient());

        $connectionConfiguration = new ConnectionConfiguration('http://test.com', 'test', 'test');
        $client = new Client($connectionConfiguration);
        $participantService = new ParticipantService($client);

        static::assertEquals($client, $participantService->getClient());
    }

    public function testGetSurveyParticipants()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(3);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertCount(0, $this->serviceWithoutParticipants->getSurveyParticipants(1));
        static::assertCount(0, $this->serviceWithoutParticipants->getSurveyParticipants(2));

        static::assertCount(2, $this->serviceWithParticipants->getSurveyParticipants(1));
        static::assertCount(1, $this->serviceWithParticipants->getSurveyParticipants(2));
        static::assertCount(0, $this->serviceWithParticipants->getSurveyParticipants(3));
    }

    public function testGetSurveyParticipantsWithImportantException()
    {
        $this->expectException(CannotProcessDataException::class);
        $exception = new CannotProcessDataException(ReasonType::NO_TOKEN_TABLE);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);
        $this->serviceWithParticipants->getSurveyParticipants(3);
    }

    public function testGetSurveyParticipantsWithNoParticipantsException()
    {
        $exception = new CannotProcessDataException(ReasonType::NO_PARTICIPANTS_FOUND);

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);
        static::assertCount(0, $this->serviceWithParticipants->getSurveyParticipants(3));
    }

    public function testHasParticipant()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(3);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertFalse($this->serviceWithoutParticipants->hasParticipant(1, 'john@scott.com'));
        static::assertFalse($this->serviceWithoutParticipants->hasParticipant(2, 'john@scott.com'));

        static::assertTrue($this->serviceWithParticipants->hasParticipant(1, 'john@scott.com'));
        static::assertFalse($this->serviceWithParticipants->hasParticipant(2, 'john@scott.com'));
        static::assertFalse($this->serviceWithParticipants->hasParticipant(3, 'john@scott.com'));
    }

    public function testAddParticipantForNotExistingSurvey()
    {
        $this->expectException(CannotProcessDataException::class);
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
                'firstname' => $firstName,
                'lastname'  => $lastName,
                'email'     => $email,
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager($runMethodCallCount, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $result = $this->serviceWithoutParticipants->addParticipant($surveyId, $firstName, $lastName, $email);

        static::assertInstanceOf(Participant::class, $result);
        static::assertEquals($firstName, $result->getFirstName());
        static::assertEquals($lastName, $result->getLastName());
        static::assertEquals($email, $result->getEmail());
    }

    public function testGetParticipant()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertNull($this->serviceWithoutParticipants->getParticipant(1, 'john@scott.com'));
        $participant = $this->serviceWithParticipants->getParticipant(1, 'john@scott.com');
        static::assertInstanceOf(Participant::class, $participant);

        static::assertEquals('John', $participant->getFirstName());
        static::assertEquals('Scott', $participant->getLastName());
        static::assertEquals('john@scott.com', $participant->getEmail());
    }

    public function testHasParticipantFilledSurveyWithException()
    {
        $this->expectException(MissingParticipantOfSurveyException::class);

        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithoutParticipants->hasParticipantFilledSurvey(1, 'john@scott.com');
    }

    public function testHasParticipantFilledSurveyUsingExistingParticipant()
    {
        $runMethodCallResults = [
            'firstname' => 'John',
            'lastname'  => 'Scott',
            'email'     => 'john@scott.com',
            'completed' => 'Y',
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(1, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertTrue($this->serviceWithParticipants->hasParticipantFilledSurvey(1, 'john@scott.com'));
    }

    public function testHasParticipantFilledSurveyUsingNotExistingParticipant()
    {
        $this->expectException(MissingParticipantOfSurveyException::class);

        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithParticipants->hasParticipantFilledSurvey(3, 'mary@jane.com');
    }

    /**
     * Returns configuration used while connecting to LimeSurvey's API
     *
     * @return ConnectionConfiguration
     */
    private function getConnectionConfiguration()
    {
        return new ConnectionConfiguration('http://test.com', 'test', 'test');
    }

    /**
     * Returns manager of session started while connecting to LimeSurvey's API
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getSessionManager()
    {
        return $this->createMock(SessionManager::class);
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
        $rpcClientManager = $this->createMock(JsonRpcClientManager::class);

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
        $rpcClientManager = $this->createMock(JsonRpcClientManager::class);

        $rpcClientManager
            ->expects(static::exactly($runMethodCallCount))
            ->method('runMethod')
            ->willThrowException($exception);

        return $rpcClientManager;
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
        $this->serviceWithoutParticipants = new ParticipantService($client);
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
                new Participant([
                    'firstname' => 'John',
                    'lastname'  => 'Scott',
                    'email'     => 'john@scott.com',
                    'completed' => 'Y',
                ]),
                new Participant([
                    'firstname' => 'Mary',
                    'lastname'  => 'Jane',
                    'email'     => 'mary@jane.com',
                ]),
            ]),
            2 => new Collection([
                new Participant(),
            ]),
        ]);

        $this->serviceWithParticipants = new ParticipantService($client, $allParticipants);
    }
}
