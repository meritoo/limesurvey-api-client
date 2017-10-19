<?php

namespace Meritoo\Common\Type;

use Meritoo\Common\Type\Base\BaseType;

/**
 * The visibility of a property, a method or (as of PHP 7.1.0) a constant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 *
 * @see       http://php.net/manual/en/language.oop5.visibility.php
 */
class OopVisibilityType extends BaseType
{
    /**
     * The "private" visibility of OOP
     *
     * @var int
     */
    const IS_PRIVATE = 3;

    /**
     * The "protected" visibility of OOP
     *
     * @var int
     */
    const IS_PROTECTED = 2;

    /**
     * The "public" visibility of OOP
     *
     * @var int
     */
    const IS_PUBLIC = 1;
}
