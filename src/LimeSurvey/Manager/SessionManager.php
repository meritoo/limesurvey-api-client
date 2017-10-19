<?php

namespace Meritoo\LimeSurvey\ApiClient\Manager;

use Meritoo\LimeSurvey\ApiClient\Exception\CreateSessionKeyFailedException;
use Meritoo\LimeSurvey\ApiClient\Type\SystemMethodType;

/**
 * Manager of session started while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SessionManager
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Manager\SessionManager';

    /**
     * The session key.
     * Used to authenticate user while connecting to LimeSurvey's API.
     *
     * @var string
     */
    private $sessionKey;

    /**
     * Manager of the JsonRPC client used while connecting to LimeSurvey's API
     *
     * @var JsonRpcClientManager
     */
    private $rpcClientManager;

    /**
     * Class constructor
     *
     * @param JsonRpcClientManager $rpcClientManager Manager of the JsonRPC client used while connecting to
     *                                               LimeSurvey's API
     */
    public function __construct(JsonRpcClientManager $rpcClientManager)
    {
        $this->rpcClientManager = $rpcClientManager;
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->releaseSessionKey();
    }

    /**
     * Returns key/id of session used while connecting to LimeSurvey's API
     *
     * @param string $username Name of user used to authenticate to LimeSurvey
     * @param string $password Password used to authenticate to LimeSurvey
     * @return string
     *
     * @throws CreateSessionKeyFailedException
     */
    public function getSessionKey($username, $password)
    {
        if (null === $this->sessionKey) {
            $arguments = [
                $username,
                $password,
            ];

            /*
             * Let's fetch the key/id of session
             */
            $this->sessionKey = $this
                ->rpcClientManager
                ->runMethod(SystemMethodType::GET_SESSION_KEY, $arguments);

            /*
             * Oops, something is broken
             */
            if (is_array($this->sessionKey)) {
                $reason = '';

                /*
                 * The "status" is provided?
                 * It's a reason of failure
                 */
                if (isset($this->sessionKey['status'])) {
                    $reason = $this->sessionKey['status'];
                }

                throw new CreateSessionKeyFailedException($reason);
            }
        }

        return $this->sessionKey;
    }

    /**
     * Releases key/id of session and closes the RPC session
     *
     * @return $this
     */
    public function releaseSessionKey()
    {
        $arguments = [
            $this->sessionKey,
        ];

        /*
         * Let's release the key/id of session
         */
        $this
            ->rpcClientManager
            ->runMethod(SystemMethodType::RELEASE_SESSION_KEY, $arguments);

        return $this;
    }
}
