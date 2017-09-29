<?php

namespace Meritoo\LimeSurvey\ApiClient\Manager;

use JsonRPC\Client as RpcClient;
use JsonRPC\Exception\InvalidJsonFormatException;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;
use Meritoo\LimeSurvey\ApiClient\Exception\InvalidResultOfMethodRunException;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Manager of the JsonRPC client used while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class JsonRpcClientManager
{
    /**
     * Configuration used while connecting to LimeSurvey's API
     *
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * The JsonRPC client used while connecting to LimeSurvey's API
     *
     * @var RpcClient
     */
    private $rpcClient;

    /**
     * Class constructor
     *
     * @param ConnectionConfiguration $configuration Configuration used while connecting to LimeSurvey's API
     */
    public function __construct(ConnectionConfiguration $configuration)
    {
        $this->connectionConfiguration = $configuration;
    }

    /**
     * Runs given method with given arguments and returns raw data
     *
     * @param string $method    Name of method to call. One of the MethodType class constants.
     * @param array  $arguments (optional) Arguments of the method to call
     * @return mixed
     *
     * @throws UnknownMethodException
     * @throws InvalidResultOfMethodRunException
     */
    public function runMethod($method, $arguments = [])
    {
        $result = null;
        $method = MethodType::getValidatedMethod($method);

        try {
            $result = $this
                ->getRpcClient()
                ->execute($method, $arguments);
        } catch (InvalidJsonFormatException $exception) {
            throw new InvalidResultOfMethodRunException($exception, $method);
        }

        return $result;
    }

    /**
     * Returns the JsonRPC client used while connecting to LimeSurvey's API
     *
     * @return RpcClient
     */
    protected function getRpcClient()
    {
        if (null === $this->rpcClient) {
            /*
             * Let's prepare the JsonRPC Client
             */
            $url = $this->connectionConfiguration->getFullUrl();
            $this->rpcClient = new RpcClient($url);

            /*
             * The "debug" mode is turned on?
             */
            if ($this->connectionConfiguration->isDebugModeOn()) {
                $this
                    ->rpcClient
                    ->getHttpClient()
                    ->withDebug();
            }

            /*
             * The SSL certificate verification is turned off?
             */
            if (!$this->connectionConfiguration->isVerifySslCertificateOn()) {
                $this
                    ->rpcClient
                    ->getHttpClient()
                    ->withoutSslVerification();
            }
        }

        return $this->rpcClient;
    }
}
