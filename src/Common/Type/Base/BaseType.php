<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Type\Base;

use Meritoo\Common\Utilities\Reflection;

/**
 * Base / abstract type of something, e.g. type of button, order, date etc.
 * Child class should contain constants - each of them represent one type.
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseType
{
    /**
     * All types
     *
     * @var array
     */
    private $all;

    /**
     * Returns all types
     *
     * @return array
     */
    public function getAll()
    {
        if (null === $this->all) {
            $this->all = Reflection::getConstants($this);
        }

        return $this->all;
    }

    /**
     * Returns information if given type is correct
     *
     * @param string $type The type to check
     * @return bool
     */
    public function isCorrectType($type)
    {
        return in_array($type, $this->getAll(), true);
    }
}
