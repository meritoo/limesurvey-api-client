<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Client;

use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Manager\JsonRpcClientManager;
use Meritoo\LimeSurvey\ApiClient\Manager\SessionManager;
use Meritoo\LimeSurvey\ApiClient\Result\Result;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Client of the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Client
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Client\Client';

    /**
     * Configuration used while connecting to LimeSurvey's API
     *
     * @var ConnectionConfiguration
     */
    private $configuration;

    /**
     * Manager of the JsonRPC client used while connecting to LimeSurvey's API
     *
     * @var JsonRpcClientManager
     */
    private $rpcClientManager;

    /**
     * Manager of session started while connecting to LimeSurvey's API
     *
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * Class constructor
     *
     * @param ConnectionConfiguration $configuration    Configuration used while connecting to LimeSurvey's API
     * @param JsonRpcClientManager    $rpcClientManager (optional) Manager of the JsonRPC client used while
     *                                                  connecting to LimeSurvey's API
     * @param SessionManager          $sessionManager   (optional) Manager of session started while connecting to
     *                                                  LimeSurvey's API
     */
    public function __construct(
        ConnectionConfiguration $configuration,
        JsonRpcClientManager $rpcClientManager = null,
        SessionManager $sessionManager = null
    ) {
        $this->configuration = $configuration;
        $this->rpcClientManager = $rpcClientManager;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Runs method with given name, arguments and returns result
     *
     * @param string $method    Name of method to call. One of the MethodType class constants.
     * @param array  $arguments (optional) Arguments of the method to call
     * @return Result
     *
     * @throws UnknownMethodException
     * @throws UnknownInstanceOfResultItem
     */
    public function run($method, $arguments = [])
    {
        /*
         * Let's validate method.
         * It's called in the JsonRpcClientManager::runMethod() too, but I want to verify it before getting session key.
         */
        $method = MethodType::getValidatedMethod($method);

        /*
         * Prepare key of session
         */
        $username = $this->configuration->getUsername();
        $password = $this->configuration->getPassword();

        $sessionKey = $this
            ->getSessionManager()
            ->getSessionKey($username, $password);

        /*
         * Use the session's key as one of the method's arguments
         */
        array_unshift($arguments, $sessionKey);

        /*
         * Run the method, fetch raw data and finally prepare result
         */
        $rawData = $this
            ->getRpcClientManager()
            ->runMethod($method, $arguments);

        /*
         * Raw data is unknown?
         * Let's use an empty array instead
         *
         * Required to avoid bug:
         * Argument 2 passed to Meritoo\LimeSurvey\ApiClient\Result\Result::__construct() must be of the type array,
         * null given
         */
        if (null === $rawData) {
            $rawData = [];
        }

        return new Result($method, $rawData);
    }

    /**
     * Returns configuration used while connecting to LimeSurvey's API
     *
     * @return ConnectionConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns manager of the JsonRPC client used while connecting to LimeSurvey's API
     *
     * @return JsonRpcClientManager
     */
    private function getRpcClientManager()
    {
        if (null === $this->rpcClientManager) {
            $this->rpcClientManager = new JsonRpcClientManager($this->configuration);
        }

        return $this->rpcClientManager;
    }

    /**
     * Returns manager of session started while connecting to LimeSurvey's API
     *
     * @return SessionManager
     */
    private function getSessionManager()
    {
        if (null === $this->sessionManager) {
            $rpcClientManager = $this->getRpcClientManager();
            $this->sessionManager = new SessionManager($rpcClientManager);
        }

        return $this->sessionManager;
    }
}
