<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Service;

use DateTime;
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
use Meritoo\LimeSurvey\ApiClient\Result\Collection\ParticipantsDetails;
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
     * Raw data of participants
     *
     * @var array
     */
    private $participantsRawData;

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
        static::assertConstructorVisibilityAndArguments(ParticipantService::className, OopVisibilityType::IS_PUBLIC, 2, 1);
    }

    public function testGetClient()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();

        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertInstanceOf(Client::className, $this->serviceWithoutParticipants->getClient());
        static::assertInstanceOf(Client::className, $this->serviceWithParticipants->getClient());

        $connectionConfiguration = $this->getConnectionConfiguration();
        $client = new Client($connectionConfiguration);
        $participantService = new ParticipantService($client);

        static::assertEquals($client, $participantService->getClient());
    }

    public function testHasParticipantUsingServiceWithoutParticipants()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(2);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);

        static::assertFalse($this->serviceWithoutParticipants->hasParticipant(1, 'john@scott.com'));
        static::assertFalse($this->serviceWithoutParticipants->hasParticipant(2, 'john@scott.com'));
    }

    public function testHasParticipant()
    {
        $runMethodCallResults = [
            [
                null,
            ],
            [
                null,
            ],
        ];

        $rpcClientManager = $this->getJsonRpcClientManager(2, $runMethodCallResults);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertTrue($this->serviceWithParticipants->hasParticipant(1, 'john@scott.com'));
        static::assertFalse($this->serviceWithParticipants->hasParticipant(2, 'john@scott.com'));
        static::assertFalse($this->serviceWithParticipants->hasParticipant(3, 'john@scott.com'));
    }

    public function testGetParticipantDetailsWithException()
    {
        $exception = new CannotProcessDataException(ReasonType::NOT_EXISTING_SURVEY_ID);
        $this->setExpectedException(CannotProcessDataException::className, $exception->getMessage());

        $rpcClientManager = $this->getJsonRpcClientManagerWithException(1, $exception);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithParticipants->getParticipantDetails(1, 'lorem@ipsum.com');
    }

    public function testGetParticipantDetails()
    {
        $sessionManager = $this->getSessionManager();

        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);

        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $participant1 = $this->serviceWithoutParticipants->getParticipantDetails(1, 'john@scott.com');
        $participant2 = $this->serviceWithParticipants->getParticipantDetails(1, 'john@scott.com');

        $rawData = $this->participantsRawData[0];
        $id = $rawData['tid'];
        $firstName = $rawData['firstname'];
        $lastName = $rawData['lastname'];
        $email = $rawData['email'];
        $token = $rawData['token'];

        static::assertNull($participant1);
        static::assertInstanceOf(Participant::className, $participant2);
        static::assertEquals($id, $participant2->getId());
        static::assertEquals($firstName, $participant2->getFirstName());
        static::assertEquals($lastName, $participant2->getLastName());
        static::assertEquals($email, $participant2->getEmail());
        static::assertEquals($token, $participant2->getToken());
        static::assertTrue($participant2->isSent());
        static::assertTrue($participant2->isCompleted());
        static::assertFalse($participant2->isBlacklisted());
        static::assertNull($participant2->getValidFrom());
    }

    public function testHasParticipantFilledSurveyWithoutParticipants()
    {
        $this->setExpectedException(MissingParticipantOfSurveyException::className);

        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithoutParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithoutParticipants->hasParticipantFilledSurvey(1, 'john@scott.com');
    }

    public function testHasParticipantFilledSurveyUsingExistingParticipant()
    {
        $rpcClientManager = $this->getJsonRpcClientManager(0);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        static::assertTrue($this->serviceWithParticipants->hasParticipantFilledSurvey(1, 'john@scott.com'));
    }

    public function testHasParticipantFilledSurveyUsingNotExistingParticipant()
    {
        $this->setExpectedException(MissingParticipantOfSurveyException::className);

        $rpcClientManager = $this->getJsonRpcClientManager(1);
        $sessionManager = $this->getSessionManager();
        $this->createServiceWithParticipants($rpcClientManager, $sessionManager);

        $this->serviceWithParticipants->hasParticipantFilledSurvey(3, 'mary@jane.com');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->participantsRawData = [
            [
                'tid'            => 1,
                'participant_id' => null,
                'mpid'           => null,
                'firstname'      => 'John',
                'lastname'       => 'Scott',
                'email'          => 'john@scott.com',
                'emailstatus'    => 'OK',
                'token'          => uniqid(),
                'language'       => 'pl',
                'blacklisted'    => 'N',
                'sent'           => 'Y',
                'remindersent'   => 'N',
                'remindercount'  => 0,
                'completed'      => (new DateTime())->format('Y-m-d H:i:s'),
                'usesleft'       => 10,
                'validfrom'      => null,
                'validuntil'     => (new DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'tid'            => 2,
                'participant_id' => null,
                'mpid'           => null,
                'firstname'      => 'Mary',
                'lastname'       => 'Jane',
                'email'          => 'mary@jane.com',
                'emailstatus'    => 'OK',
                'token'          => uniqid(),
                'language'       => 'pl',
                'blacklisted'    => 'N',
                'sent'           => 'Y',
                'remindersent'   => 'N',
                'remindercount'  => 0,
                'completed'      => 'N',
                'usesleft'       => 10,
                'validfrom'      => null,
                'validuntil'     => (new DateTime())->format('Y-m-d H:i:s'),
            ],
        ];
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

        $participantsDetails = new ParticipantsDetails([
            1 => new Collection([
                new Participant($this->participantsRawData[0]),
                new Participant($this->participantsRawData[1]),
            ]),
            2 => new Collection([
                new Participant(),
            ]),
        ]);

        $this->serviceWithParticipants = new ParticipantService($client, $participantsDetails);
    }
}
