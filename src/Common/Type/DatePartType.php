<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Type;

use Meritoo\Common\Type\Base\BaseType;

/**
 * Type of date part, e.g. "year"
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class DatePartType extends BaseType
{
    /**
     * The "day" date part
     *
     * @var string
     */
    const DAY = 'day';

    /**
     * The "hour" date part
     *
     * @var string
     */
    const HOUR = 'hour';

    /**
     * The "minute" date part
     *
     * @var string
     */
    const MINUTE = 'minute';

    /**
     * The "month" date part
     *
     * @var string
     */
    const MONTH = 'month';

    /**
     * The "second" date part
     *
     * @var string
     */
    const SECOND = 'second';

    /**
     * The "year" date part
     *
     * @var string
     */
    const YEAR = 'year';
}
