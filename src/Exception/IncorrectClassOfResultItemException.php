<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * An exception used while class used to create instance of one item of the result, with data fetched from the
 * LimeSurvey's API, is incorrect
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class IncorrectClassOfResultItemException extends \Exception
{
    /**
     * Class constructor
     *
     * @param string $className Incorrect class name used to create instance of one item
     */
    public function __construct($className)
    {
        $template = 'Class %s used to create instance of one item of the result should extend %s, but it does not. Did'
            . ' you forget to use proper base class?';

        $message = sprintf($template, $className, BaseItem::class);
        parent::__construct($message);
    }
}
