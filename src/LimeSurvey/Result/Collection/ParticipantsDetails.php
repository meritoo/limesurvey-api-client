<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Collection;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseParticipantsCollection;

/**
 * Collection of participants' full data.
 * All participants grouped per survey.
 *
 * It's a collection of participants' collections.
 * The survey ID is used as an index per each collection of participants, so they are grouped by survey.
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantsDetails extends BaseParticipantsCollection
{
    /**
     * Returns information if survey with given ID has participant with given e-mail address
     *
     * @param int    $surveyId         ID of survey
     * @param string $participantEmail E-mail of searched participant
     * @return bool
     */
    public function hasParticipantOfSurvey($surveyId, $participantEmail)
    {
        return null !== $this->getParticipantOfSurvey($surveyId, $participantEmail);
    }
}
