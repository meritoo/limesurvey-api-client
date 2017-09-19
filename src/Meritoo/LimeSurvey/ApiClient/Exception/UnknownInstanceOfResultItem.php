<?php

namespace Meritoo\LimeSurvey\ApiClient\Exception;

use Exception;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;

/**
 * An exception used while instance of one item used by result, with data fetched from the LimeSurvey's API, is unknown
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class UnknownInstanceOfResultItem extends Exception
{
    /**
     * Class constructor
     *
     * @param string $method Name of called method while talking to the LimeSurvey's API. One of the MethodType class
     *                       constants.
     */
    public function __construct($method)
    {
        $template = 'Instance of one item used by result the of \'%s\' LimeSurvey API\'s method is unknown. Proper'
            . ' class is not mapped in %s::%s() method. Did you forget about this?';

        $message = sprintf($template, $method, ResultProcessor::class, 'getItemInstance');
        parent::__construct($message);
    }
}
