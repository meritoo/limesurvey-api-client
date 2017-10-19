<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Exception\File;

/**
 * An exception used while file with given path does not exist
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class NotExistingFileException extends \Exception
{
    const className = 'Meritoo\Common\Exception\File\NotExistingFileException';

    /**
     * Class constructor
     *
     * @param string $notExistingFilePath Path of not existing (or not readable) file
     */
    public function __construct($notExistingFilePath)
    {
        $template = 'File with path \'%s\' does not exist (or is not readable). Did you provide path of proper file?';
        $message = sprintf($template, $notExistingFilePath);

        parent::__construct($message);
    }
}
