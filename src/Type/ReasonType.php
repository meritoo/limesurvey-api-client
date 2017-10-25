<?php

namespace Meritoo\LimeSurvey\ApiClient\Type;

use Meritoo\Common\Type\Base\BaseType;

/**
 * Type of reason used by LimeSurvey's exception
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ReasonType extends BaseType
{
    /**
     * Reason of exception when there is no survey with given ID
     *
     * @var string
     */
    const NOT_EXISTING_SURVEY_ID = 'Error: Invalid survey ID';

    /**
     * Reason of exception when there is no participants of survey
     *
     * @var string
     */
    const NO_PARTICIPANTS_FOUND = 'No survey participants found.';

    /**
     * Reason of exception when there is no participant's properties/details
     *
     * @var string
     */
    const NO_PARTICIPANT_PROPERTIES = 'Error: No results were found based on your attributes.';

    /**
     * Reason of exception when there is no surveys
     *
     * @var string
     */
    const NO_SURVEYS_FOUND = 'No surveys found';

    /**
     * Reason of exception when there is no table with tokens/participants of survey
     *
     * @var string
     */
    const NO_TOKEN_TABLE = 'Error: No token table';
}
