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
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');

        static::assertEquals('http://test.com', $configuration->getBaseUrl());
        static::assertEquals('test1', $configuration->getUsername());
        static::assertEquals('test2', $configuration->getPassword());
        static::assertFalse($configuration->isDebugModeOn());
    }

    public function testSetBaseUrl()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');

        $configuration->setBaseUrl('http://lorem.ipsum');
        static::assertEquals('http://lorem.ipsum', $configuration->getBaseUrl());

        $configuration->setBaseUrl('http://lorem.ipsum/');
        static::assertEquals('http://lorem.ipsum', $configuration->getBaseUrl());
    }

    public function testSetRemoteControlUrl()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
        $configuration->setRemoteControlUrl('/lorem/ipsum');

        static::assertEquals('/lorem/ipsum', $configuration->getRemoteControlUrl());
    }

    public function testSetUsername()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
        $configuration->setUsername('lorem');

        static::assertEquals('lorem', $configuration->getUsername());
    }

    public function testSetPassword()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
        $configuration->setPassword('ipsum');

        static::assertEquals('ipsum', $configuration->getPassword());
    }

    public function testSetDebugMode()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');

        $configuration->setDebugMode();
        static::assertFalse($configuration->isDebugModeOn());

        $configuration->setDebugMode(false);
        static::assertFalse($configuration->isDebugModeOn());

        $configuration->setDebugMode(true);
        static::assertTrue($configuration->isDebugModeOn());
    }

    public function testGetFullUrl()
    {
        $configuration = new ConnectionConfiguration('http://test.com', 'test1', 'test2');
        $configuration->setRemoteControlUrl('lorem/ipsum');

        static::assertEquals('http://test.com/lorem/ipsum', $configuration->getFullUrl());
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
}
