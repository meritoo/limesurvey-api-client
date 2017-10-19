<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\Date;

use Meritoo\Common\Exception\Base\UnknownTypeException;
use Meritoo\Common\Type\DatePartType;

/**
 * An exception used while type of date part, e.g. "year", is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class UnknownDatePartTypeException extends UnknownTypeException
{
    /**
     * Class constructor
     *
     * @param string $unknownDatePart Type of date part, e.g. "year". One of DatePartType class constants.
     * @param string $value           Incorrect value
     */
    public function __construct($unknownDatePart, $value)
    {
        parent::__construct($unknownDatePart, new DatePartType(), sprintf('date part (with value %s)', $value));
    }
}
