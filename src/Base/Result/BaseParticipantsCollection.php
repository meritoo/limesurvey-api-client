<?php

namespace Meritoo\LimeSurvey\ApiClient\Base\Result;

use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Exception\Method\DisabledMethodException;

/**
 * Base class for participants' collection
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseParticipantsCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function add($element, $index = null)
    {
        throw DisabledMethodException::create(__METHOD__, 'addParticipant');
    }

    /**
     * {@inheritdoc}
     */
    public function addMultiple($elements, $useIndexes = false)
    {
        throw DisabledMethodException::create(__METHOD__, 'addParticipants');
    }

    /**
     * {@inheritdoc}
     */
    public function has($element)
    {
        throw DisabledMethodException::create(__METHOD__, 'hasParticipantsOfSurvey');
    }

    /**
     * Adds participants of given survey
     *
     * @param Collection $participants Participants to add. Collection of ParticipantShort or Participant instances.
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
     * Adds participant of given survey
     *
     * @param BaseParticipant $participant Participant to add
     * @param int             $surveyId    ID of survey
     * @return $this
     */
    public function addParticipant(BaseParticipant $participant, $surveyId)
    {
        $this
            ->getBySurvey($surveyId)
            ->add($participant);

        return $this;
    }

    /**
     * Returns participant of given survey
     *
     * @param int    $surveyId         ID of survey
     * @param string $participantEmail E-mail of searched participant
     * @return BaseParticipant|null
     */
    public function getParticipantOfSurvey($surveyId, $participantEmail)
    {
        $participants = $this->getBySurvey($surveyId);

        /*
         * No participants?
         * Nothing to do
         */
        if ($participants->isEmpty()) {
            return null;
        }

        foreach ($participants as $participant) {
            if ($participant->getEmail() == $participantEmail) {
                return $participant;
            }
        }

        return null;
    }
}
