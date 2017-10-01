<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

/**
 * An exception used when participant of survey is missing
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MissingParticipantOfSurveyException extends \Exception
{
    /**
     * Class constructor
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     */
    public function __construct($surveyId, $email)
    {
        $template = 'Participant with e-mail %s of survey with ID %s is missing. Maybe was not added to the survey?';
        $message = sprintf($template, $email, $surveyId);

        parent::__construct($message);
    }
}
