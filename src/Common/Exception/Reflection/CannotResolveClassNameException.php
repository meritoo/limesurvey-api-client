<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\Reflection;

use Exception;

/**
 * An exception used while name of class or trait cannot be resolved
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class CannotResolveClassNameException extends Exception
{
    /**
     * Class constructor
     *
     * @param array|object|string $source   Source of the class's / trait's name. It can be an array of objects,
     *                                      namespaces, object or namespace.
     * @param bool                $forClass (optional) If is set to true, message of this exception for class is
     *                                      prepared. Otherwise - for trait.
     */
    public function __construct($source, $forClass = true)
    {
        $forWho = 'trait';
        $value = '';

        if ($forClass) {
            $forWho = 'class';
        }

        if (is_scalar($source)) {
            $value = sprintf(' %s', (string)$source);
        }

        $template = 'Name of %s from given \'%s\'%s cannot be resolved. Is there everything ok?';
        $message = sprintf($template, $forWho, gettype($source), $value);

        parent::__construct($message);
    }
}
