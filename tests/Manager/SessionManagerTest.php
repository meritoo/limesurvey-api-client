<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Manager;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\CreateSessionKeyFailedException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;

/**
 * Test case of the manager of session started while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SessionManagerTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(SessionManager::class, OopVisibilityType::IS_PUBLIC, 1, 1);
    }

    public function testGetSessionKeyWhenFailedWithoutReason()
    {
        $this->expectException(CreateSessionKeyFailedException::class);
        $this->expectExceptionMessage('Create of the session key has failed');

        $clientManager = $this->createMock(JsonRpcClientManager::class);

        $clientManager
            ->expects(static::any())
            ->method('runMethod')
            ->willReturn([]);

        (new SessionManager($clientManager))->getSessionKey('lorem', 'ipsum');
    }

    public function testGetSessionKeyWhenFailedWithReason()
    {
        $reason = 'Invalid credentials';

        $this->expectException(CreateSessionKeyFailedException::class);
        $this->expectExceptionMessage(sprintf('Create of the session key has failed. Reason: \'%s\'.', $reason));

        $clientManager = $this->createMock(JsonRpcClientManager::class);

        $clientManager
            ->expects(static::any())
            ->method('runMethod')
            ->willReturn([
                'status' => $reason,
            ]);

        (new SessionManager($clientManager))->getSessionKey('lorem', 'ipsum');
    }

    public function testGetSessionKey()
    {
        $clientManager = $this->createMock(JsonRpcClientManager::class);

        $clientManager
            ->expects(static::any())
            ->method('runMethod')
            ->willReturn('12345');

        $sessionManager = new SessionManager($clientManager);
        static::assertEquals('12345', $sessionManager->getSessionKey('lorem', 'ipsum'));
    }

    public function testReleaseSessionKey()
    {
        $clientManager = $this->createMock(JsonRpcClientManager::class);

        $clientManager
            ->expects(static::any())
            ->method('runMethod')
            ->willReturn([]);

        $sessionManager = new SessionManager($clientManager);
        static::assertInstanceOf(SessionManager::class, $sessionManager->releaseSessionKey());
    }
}
