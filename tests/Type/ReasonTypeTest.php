<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Type;

use Meritoo\Common\Test\Base\BaseTypeTestCase;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;

/**
 * Test case of the type of reason used by LimeSurvey's exception
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ReasonTypeTest extends BaseTypeTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertHasNoConstructor(ReasonType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllExpectedTypes()
    {
        return [
            'NO_PARTICIPANTS_FOUND' => ReasonType::NO_PARTICIPANTS_FOUND,
            'NO_SURVEYS_FOUND'      => ReasonType::NO_SURVEYS_FOUND,
            'NO_TOKEN_TABLE'        => ReasonType::NO_TOKEN_TABLE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTestedTypeInstance()
    {
        return new ReasonType();
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
            ReasonType::NO_PARTICIPANTS_FOUND,
            true,
        ];

        yield[
            ReasonType::NO_SURVEYS_FOUND,
            true,
        ];

        yield[
            ReasonType::NO_TOKEN_TABLE,
            true,
        ];
    }
}
