<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Base\Result;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * Test case of the base class for one item of result/data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class BaseItemTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(BaseItem::class, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testSetValuesVisibilityAndArguments()
    {
        static::assertMethodVisibilityAndArguments(BaseItem::class, 'setValues', OopVisibilityType::IS_PRIVATE, 1, 1);
    }
}
