<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\Regex;

/**
 * An exception used while length of given hexadecimal value of color is incorrect
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class IncorrectColorHexLengthException extends \Exception
{
    /**
     * Class constructor
     *
     * @param string $color Incorrect hexadecimal value of color
     */
    public function __construct($color)
    {
        $template = 'Length of hexadecimal value of color \'%s\' is incorrect. It\'s %d, but it should be 3 or 6.'
            . ' Is there everything ok?';

        $message = sprintf($template, $color, strlen($color));
        parent::__construct($message);
    }
}
