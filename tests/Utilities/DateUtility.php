<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Utilities;

use DateTime;

/**
 * Date-related utility
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class DateUtility
{
    /**
     * Returns date formatted with long or medium format
     *
     * @param bool $useLongFormat (optional) If is set to true, long format will be used (default behaviour).
     *                            Otherwise - medium format.
     * @return string
     */
    public static function getDateTime($useLongFormat = true)
    {
        $format = 'Y-m-d H:i';

        if ($useLongFormat) {
            $format = 'Y-m-d H:i:s';
        }

        return (new DateTime())->format($format);
    }
}
