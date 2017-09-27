<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Configuration;

use Generator;
use Meritoo\Common\Exception\Regex\InvalidUrlException;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Configuration\ConnectionConfiguration;

/**
 * Test case of the configuration used while connecting to LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ConnectionConfigurationTest extends BaseTestCase
{
    /**
     * Simple instance of the configuration
     *
     * @var ConnectionConfiguration
     */
    private $simpleConfiguration;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(ConnectionConfiguration::class, OopVisibilityType::IS_PUBLIC, 5, 3);
    }

    /**
     * @param mixed $emptyBaseUrl Empty base url
     * @dataProvider provideEmptyBaseUrl
     */
    public function testConstructorWithEmptyBaseUrl($emptyBaseUrl)
    {
        $this->expectException(InvalidUrlException::class);
        new ConnectionConfiguration($emptyBaseUrl, '', '');
    }

    /**
     * @param string $invalidBaseUrl Invalid base url
     * @dataProvider provideInvalidBaseUrl
     */
    public function testConstructorWithInvalidBaseUrl($invalidBaseUrl)
    {
        $this->expectException(InvalidUrlException::class);
        new ConnectionConfiguration($invalidBaseUrl, '', '');
    }

    public function testConstructor()
    {
        static::assertEquals('http://test.com', $this->simpleConfiguration->getBaseUrl());
        static::assertEquals('test1', $this->simpleConfiguration->getUsername());
        static::assertEquals('test2', $this->simpleConfiguration->getPassword());
        static::assertFalse($this->simpleConfiguration->isDebugModeOn());
    }

    public function testSetBaseUrl()
    {
        $this->simpleConfiguration->setBaseUrl('http://lorem.ipsum');
        static::assertEquals('http://lorem.ipsum', $this->simpleConfiguration->getBaseUrl());

        $this->simpleConfiguration->setBaseUrl('http://lorem.ipsum/');
        static::assertEquals('http://lorem.ipsum', $this->simpleConfiguration->getBaseUrl());
    }

    public function testSetRemoteControlUrl()
    {
        $this->simpleConfiguration->setRemoteControlUrl('/lorem/ipsum');
        static::assertEquals('/lorem/ipsum', $this->simpleConfiguration->getRemoteControlUrl());
    }

    public function testSetUsername()
    {
        $this->simpleConfiguration->setUsername('lorem');
        static::assertEquals('lorem', $this->simpleConfiguration->getUsername());
    }

    public function testSetPassword()
    {
        $this->simpleConfiguration->setPassword('ipsum');
        static::assertEquals('ipsum', $this->simpleConfiguration->getPassword());
    }

    public function testSetDebugMode()
    {
        $this->simpleConfiguration->setDebugMode();
        static::assertFalse($this->simpleConfiguration->isDebugModeOn());

        $this->simpleConfiguration->setDebugMode(false);
        static::assertFalse($this->simpleConfiguration->isDebugModeOn());

        $this->simpleConfiguration->setDebugMode(true);
        static::assertTrue($this->simpleConfiguration->isDebugModeOn());
    }

    public function testSetVerifySslCertificate()
    {
        $this->simpleConfiguration->setVerifySslCertificate();
        static::assertTrue($this->simpleConfiguration->isVerifySslCertificateOn());

        $this->simpleConfiguration->setVerifySslCertificate(false);
        static::assertFalse($this->simpleConfiguration->isVerifySslCertificateOn());

        $this->simpleConfiguration->setVerifySslCertificate(true);
        static::assertTrue($this->simpleConfiguration->isVerifySslCertificateOn());
    }

    public function testGetFullUrl()
    {
        $this->simpleConfiguration->setRemoteControlUrl('lorem/ipsum');
        static::assertEquals('http://test.com/lorem/ipsum', $this->simpleConfiguration->getFullUrl());
    }

    /**
     * Provides empty base url
     *
     * @return Generator
     */
    public function provideEmptyBaseUrl()
    {
        yield[
            '',
        ];

        yield[
            null,
        ];
    }

    /**
     * Provides invalid base url
     *
     * @return Generator
     */
    public function provideInvalidBaseUrl()
    {
        yield[
            'lorem',
        ];

        yield[
            'ipsum',
        ];

        yield[
            'htp:/dolor.com',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->simpleConfiguration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
    }
}
