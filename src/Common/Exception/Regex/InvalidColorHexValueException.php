<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\Regex;

/**
 * An exception used while given hexadecimal value of color is invalid
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class InvalidColorHexValueException extends \Exception
{
    /**
     * Class constructor
     *
     * @param string $color Invalid hexadecimal value of color
     */
    public function __construct($color)
    {
        $message = sprintf('Hexadecimal value of color \'%s\' is invalid. Is there everything ok?', $color);
        parent::__construct($message);
    }
}
