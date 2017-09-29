<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

use Exception;
use Meritoo\Common\Utilities\Arrays;

/**
 * An exception used when an error occurred while running method
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class InvalidResultOfMethodRunException extends Exception
{
    /**
     * Class constructor
     *
     * @param Exception $previousException The previous exception, source of an error
     * @param string    $methodName        Name of called method
     * @param array     $methodArguments   (optional) Arguments of the called method
     */
    public function __construct(Exception $previousException, $methodName, array $methodArguments = [])
    {
        $template = "Oops, an error occurred while running method. Is there everything ok? Details:\n"
            . "- error: %s,\n"
            . "- method: %s,\n"
            . '- arguments: %s.';

        if (empty($methodArguments)) {
            $methodArguments = '(no arguments)';
        } else {
            $methodArguments = Arrays::valuesKeys2string($methodArguments, ', ', '=', '"');
        }

        $message = sprintf($template, $previousException->getMessage(), $methodName, $methodArguments);
        parent::__construct($message, $previousException->getCode());
    }
}
