<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Type;

use Meritoo\Common\Test\Base\BaseTypeTestCase;
use Meritoo\LimeSurvey\ApiClient\Type\SystemMethodType;

/**
 * Test case of the type of system-related method used while talking with LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SystemMethodTypeTest extends BaseTypeTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getAllExpectedTypes()
    {
        return [
            'GET_SESSION_KEY'     => SystemMethodType::GET_SESSION_KEY,
            'RELEASE_SESSION_KEY' => SystemMethodType::RELEASE_SESSION_KEY,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTestedTypeInstance()
    {
        return new SystemMethodType();
    }

    /**
     * {@inheritdoc}
     */
    public function provideTypeToVerify()
    {
        yield[
            '',
            false,
        ];

        yield[
            'lorem',
            false,
        ];

        yield[
            SystemMethodType::GET_SESSION_KEY,
            true,
        ];

        yield[
            SystemMethodType::RELEASE_SESSION_KEY,
            true,
        ];
    }
}
