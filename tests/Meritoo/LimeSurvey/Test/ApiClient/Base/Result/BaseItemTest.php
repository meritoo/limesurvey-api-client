<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Base\Result;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Test case of the base class for one item of result/data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class BaseItemTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        $reflection = new ReflectionClass(BaseItem::class);
        $constructor = $reflection->getConstructor();

        static::assertNull($constructor);
    }

    public function testSetValues()
    {
        $mock = $this->getBaseItemMock();

        static::assertInstanceOf(BaseItem::class, $mock->setValues([]));
        static::assertInstanceOf(BaseItem::class, $mock->setValues(['lorem']));
    }

    /**
     * Returns mock of the tested class
     *
     * @return BaseItem
     */
    private function getBaseItemMock()
    {
        $mock = $this->getMockForAbstractClass(BaseItem::class);

        $mock
            ->expects(static::any())
            ->method('setValue')
            ->willReturn(null);

        return $mock;
    }
}
