<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Service;

use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Service\ParticipantService;
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

    public function testGetSurveyParticipantsFromEmptyParticipants()
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

    public function testAddParticipant()
    {
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

        $result = $this->serviceWithoutParticipants->addParticipant(1, $firstName, $lastName, $email);
        static::assertInstanceOf(Participant::class, $result);
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
     * Returns manager of the JsonRPC client used while connecting to LimeSurvey's API
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
                (new Participant())->setValues([
                    'firstname' => 'John',
                    'lastname'  => 'Scott',
                    'email'     => 'john@scott.com',
                ]),
                new Participant(),
            ]),
            2 => new Collection([
                new Participant(),
            ]),
        ]);

        $this->serviceWithParticipants = new ParticipantService($client, $allParticipants);
    }
}
