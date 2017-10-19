<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\File;

/**
 * An exception used while file with given path is empty (has no content)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class EmptyFileException extends \Exception
{
    /**
     * Class constructor
     *
     * @param string $emptyFilePath Path of the empty file
     */
    public function __construct($emptyFilePath)
    {
        $template = 'File with path \'%s\' is empty (has no content). Did you provide path of proper file?';
        $message = sprintf($template, $emptyFilePath);

        parent::__construct($message);
    }
}
