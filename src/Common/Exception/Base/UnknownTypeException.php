<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\Base;

use Exception;
use Meritoo\Common\Type\Base\BaseType;
use Meritoo\Common\Utilities\Arrays;

/**
 * An exception used while type of something is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class UnknownTypeException extends Exception
{
    /**
     * Class constructor
     *
     * @param string|int $unknownType  The unknown type of something (value of constant)
     * @param BaseType   $typeInstance An instance of class that contains type of the something
     * @param string     $typeName     Name of the something
     */
    public function __construct($unknownType, BaseType $typeInstance, $typeName)
    {
        $allTypes = $typeInstance->getAll();
        $types = Arrays::values2string($allTypes, '', ', ');

        $template = 'The \'%s\' type of %s is unknown. Probably doesn\'t exist or there is a typo. You should use one'
            . ' of these types: %s.';

        $message = sprintf(sprintf($template, $unknownType, $typeName, $types));
        parent::__construct($message);
    }
}
