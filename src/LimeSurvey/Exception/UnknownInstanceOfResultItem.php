<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

use Exception;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;

/**
 * An exception used while class name used to create instance of one item of the result, with data fetched from the
 * LimeSurvey's API, is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class UnknownInstanceOfResultItem extends Exception
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem';

    /**
     * Class constructor
     *
     * @param string $method Name of called method while talking to the LimeSurvey's API. One of the MethodType class
     *                       constants.
     */
    public function __construct($method)
    {
        $template = 'Class name used to create instance of one item used by result the of \'%s\' LimeSurvey API\'s'
            . ' method is unknown. Proper class is not mapped in %s::%s() method. Did you forget about this?';

        $message = sprintf($template, $method, ResultProcessor::className, 'getItemClassName');
        parent::__construct($message);
    }
}
