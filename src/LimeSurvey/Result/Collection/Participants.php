<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Collection;

use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Exception\Method\DisabledMethodException;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;

/**
 * Collection of participants' short data.
 * All participants grouped per survey.
 *
 * It's a collection of participants' collections.
 * The survey ID is used as an index per each collection of participants, so they are grouped by survey.
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Participants extends Collection
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants';

    /**
     * {@inheritdoc}
     */
    public function add($element, $index = null)
    {
        throw new DisabledMethodException(__METHOD__, 'addParticipants');
    }

    /**
     * {@inheritdoc}
     */
    public function addMultiple($elements, $useIndexes = false)
    {
        throw new DisabledMethodException(__METHOD__, 'addParticipants');
    }

    /**
     * {@inheritdoc}
     */
    public function has($element)
    {
        throw new DisabledMethodException(__METHOD__, 'hasParticipantsOfSurvey');
    }

    /**
     * Adds participants of given survey
     *
     * @param Collection $participants Participants to add. Collection of ParticipantShort classes.
     * @param int        $surveyId     ID of survey
     * @return $this
     */
    public function addParticipants(Collection $participants, $surveyId)
    {
        /*
         * No participants?
         * Nothing to do
         */
        if ($participants->isEmpty()) {
            return $this;
        }

        $this
            ->getBySurvey($surveyId)
            ->addMultiple($participants);

        return $this;
    }

    /**
     * Adds participant of given survey
     *
     * @param ParticipantShort $participant Participant to add
     * @param int              $surveyId    ID of survey
     * @return $this
     */
    public function addParticipant(ParticipantShort $participant, $surveyId)
    {
        $this
            ->getBySurvey($surveyId)
            ->add($participant);

        return $this;
    }

    /**
     * Returns information if there are participants of given survey
     *
     * @param int $surveyId ID of survey
     * @return bool
     */
    public function hasParticipantsOfSurvey($surveyId)
    {
        return false === $this
                ->getBySurvey($surveyId)
                ->isEmpty();
    }

    /**
     * Returns participants of given survey
     *
     * If there are no participants of given survey, adds an empty collection who will store participants.
     * So, this method will return collection always.
     *
     * @param int $surveyId ID of survey
     * @return Collection
     */
    public function getBySurvey($surveyId)
    {
        /*
         * There are no participants of given survey?
         * Let's add an empty collection who will store participants
         */
        if (!isset($this[$surveyId])) {
            $this[$surveyId] = new Collection();
        }

        return $this[$surveyId];
    }

    /**
     * Returns participant of given survey
     *
     * @param int    $surveyId         ID of survey
     * @param string $participantEmail E-mail of searched participant
     * @return ParticipantShort|null
     */
    public function getParticipantOfSurvey($surveyId, $participantEmail)
    {
        /* @var Collection $participants */
        $participants = $this->getBySurvey($surveyId);

        if ($participants->isEmpty()) {
            return null;
        }

        /* @var ParticipantShort $participant */
        foreach ($participants as $participant) {
            if ($participant->getEmail() == $participantEmail) {
                return $participant;
            }
        }

        return null;
    }
}
