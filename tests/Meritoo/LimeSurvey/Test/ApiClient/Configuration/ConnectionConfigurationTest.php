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
     * Configuration with default values of optional constructor's arguments
     *
     * @var ConnectionConfiguration
     */
    private $configurationWithDefaults;

    /**
     * Configuration without default values of optional constructor's arguments
     *
     * @var ConnectionConfiguration
     */
    private $configurationAnother;

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
        static::assertEquals('http://test.com', $this->configurationWithDefaults->getBaseUrl());
        static::assertEquals('test1', $this->configurationWithDefaults->getUsername());
        static::assertEquals('test2', $this->configurationWithDefaults->getPassword());
        static::assertFalse($this->configurationWithDefaults->isDebugModeOn());
        static::assertTrue($this->configurationWithDefaults->isVerifySslCertificateOn());

        static::assertEquals('http://lets-test.com', $this->configurationAnother->getBaseUrl());
        static::assertEquals('test11', $this->configurationAnother->getUsername());
        static::assertEquals('test22', $this->configurationAnother->getPassword());
        static::assertTrue($this->configurationAnother->isDebugModeOn());
        static::assertFalse($this->configurationAnother->isVerifySslCertificateOn());
    }

    public function testGetRemoteControlUrl()
    {
        $this->configurationWithDefaults->setRemoteControlUrl('lorem/ipsum');
        static::assertEquals('lorem/ipsum', $this->configurationWithDefaults->getRemoteControlUrl());

        $this->configurationAnother->setRemoteControlUrl('dolor/sit');
        static::assertEquals('dolor/sit', $this->configurationAnother->getRemoteControlUrl());
    }

    public function testGetFullUrl()
    {
        $this->configurationWithDefaults->setRemoteControlUrl('lorem/ipsum');
        static::assertEquals('http://test.com/lorem/ipsum', $this->configurationWithDefaults->getFullUrl());
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

        $this->configurationWithDefaults = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
        $this->configurationAnother = new ConnectionConfiguration('http://lets-test.com/', 'test11', 'test22', true, false);
    }
}
