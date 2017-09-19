<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Configuration;

use Meritoo\Common\Exception\Regex\InvalidUrlException;
use Meritoo\Common\Utilities\Regex;

/**
 * Configuration used while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ConnectionConfiguration
{
    /**
     * Base url.
     * Protocol & domain.
     *
     * Example:
     * http://my-domain.com
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Url of the LimeSurvey's remote control (without starting slash)
     *
     * @var string
     */
    private $remoteControlUrl = 'admin/remotecontrol';

    /**
     * Name of user used to authenticate to LimeSurvey
     *
     * @var string
     */
    private $username;

    /**
     * Password used to authenticate to LimeSurvey
     *
     * @var string
     */
    private $password;

    /**
     * If is set to true, the "debug" mode is turned on. Otherwise - turned off.
     * If turned on, more information is displayed.
     *
     * @var bool
     */
    private $debugMode = false;

    /**
     * Class constructor
     *
     * @param string $baseUrl   Base url. Protocol & domain.
     * @param string $username  Name of user used to authenticate to LimeSurvey
     * @param string $password  Password used to authenticate to LimeSurvey
     * @param bool   $debugMode (optional) If is set to true, the "debug" mode is turned on. Otherwise - turned off.
     */
    public function __construct($baseUrl, $username, $password, $debugMode = false)
    {
        $this
            ->setBaseUrl($baseUrl)
            ->setUsername($username)
            ->setPassword($password)
            ->setDebugMode($debugMode);
    }

    /**
     * Returns the base url, protocol & domain
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the base url, protocol & domain
     *
     * @param string $baseUrl The base url, protocol & domain
     * @return $this
     *
     * @throws InvalidUrlException
     */
    public function setBaseUrl($baseUrl)
    {
        if (!Regex::isValidUrl($baseUrl)) {
            throw new InvalidUrlException($baseUrl);
        }

        if (Regex::endsWith($baseUrl, '/')) {
            $baseUrl = substr($baseUrl, 0, strlen($baseUrl) - 1);
        }

        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Returns the url of the LimeSurvey's remote control
     *
     * @return string
     */
    public function getRemoteControlUrl()
    {
        return $this->remoteControlUrl;
    }

    /**
     * Sets the url of the LimeSurvey's remote control
     *
     * @param string $remoteControlUrl Url of the LimeSurvey's remote control
     * @return $this
     */
    public function setRemoteControlUrl($remoteControlUrl)
    {
        $this->remoteControlUrl = $remoteControlUrl;

        return $this;
    }

    /**
     * Returns the name of user used to authenticate to LimeSurvey
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the name of user used to authenticate to LimeSurvey
     *
     * @param string $username The name of user used to authenticate to LimeSurvey
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Returns the password used to authenticate to LimeSurvey
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password used to authenticate to LimeSurvey
     *
     * @param string $password The password used to authenticate to LimeSurvey
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns information if the "debug" mode is turned on
     *
     * @return bool
     */
    public function isDebugModeOn()
    {
        return $this->debugMode;
    }

    /**
     * Sets information if the "debug" mode is turned on
     *
     * @param bool $debugMode (optional) If is set to true, the "debug" mode is turned on. Otherwise - turned off.
     * @return $this
     */
    public function setDebugMode($debugMode = false)
    {
        $this->debugMode = $debugMode;

        return $this;
    }

    /*
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Additional / extra methods (neither getters, nor setters)
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */

    /**
     * Returns full url of the LimeSurvey's API.
     * It's a base url with part related to remote control.
     *
     * @return string
     */
    public function getFullUrl()
    {
        return sprintf('%s/%s', $this->baseUrl, $this->remoteControlUrl);
    }
}
