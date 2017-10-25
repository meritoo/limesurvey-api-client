<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

/**
 * An exception used when survey's summary is missing
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MissingSurveySummaryException extends \Exception
{
    /**
     * Class constructor
     *
     * @param int $surveyId ID of survey
     */
    public function __construct($surveyId)
    {
        $template = 'Summary of survey with ID %d is missing. Does the survey exist?';
        $message = sprintf($template, $surveyId);

        parent::__construct($message);
    }
}
