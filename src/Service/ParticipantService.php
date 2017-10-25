<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Service;

use Meritoo\Common\Collection\Collection;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
use Meritoo\LimeSurvey\ApiClient\Exception\MissingParticipantOfSurveyException;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\ParticipantsDetails;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;

/**
 * Service that serves participants
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantService
{
    /**
     * Client of the LimeSurvey's API
     *
     * @var Client
     */
    private $client;

    /**
     * Collection of participants' short data.
     * All participants grouped per survey.
     *
     * @var Participants
     */
    private $allParticipants;

    /**
     * Collection of participants' full data.
     * All participants grouped per survey.
     *
     * @var Participants
     */
    private $participantsDetails;

    /**
     * Class constructor
     *
     * @param Client              $client               Client of the LimeSurvey's API
     * @param Participants        $allParticipants      (optional) Collection of participants' short data. All participants
     *                                                  grouped per survey.
     * @param ParticipantsDetails $participantsDetails  (optional) Collection of participants' full data. All
     *                                                  participants grouped per survey.
     */
    public function __construct(
        Client $client,
        Participants $allParticipants = null,
        ParticipantsDetails $participantsDetails = null
    ) {
        if (null === $allParticipants) {
            $allParticipants = new Participants();
        }

        if (null === $participantsDetails) {
            $participantsDetails = new ParticipantsDetails();
        }

        $this->client = $client;
        $this->allParticipants = $allParticipants;
        $this->participantsDetails = $participantsDetails;
    }

    /**
     * Returns client of the LimeSurvey's API
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns participants of given survey
     *
     * @param int $surveyId ID of survey
     * @return Collection
     *
     * @throws CannotProcessDataException
     */
    public function getSurveyParticipants($surveyId)
    {
        $hasSurvey = $this
            ->allParticipants
            ->hasParticipantsOfSurvey($surveyId);

        if (!$hasSurvey) {
            $arguments = [
                $surveyId,
            ];

            try {
                $participants = $this
                    ->client
                    ->run(MethodType::LIST_PARTICIPANTS, $arguments)
                    ->getData();
            } catch (CannotProcessDataException $exception) {
                /*
                 * Oops, something is broken, because the reason is different than "there are no participants"
                 */
                if (ReasonType::NO_PARTICIPANTS_FOUND !== $exception->getReason()) {
                    throw $exception;
                }

                $participants = new Collection();
            }

            $this
                ->allParticipants
                ->addParticipants($participants, $surveyId);
        }

        return $this
            ->allParticipants
            ->getBySurvey($surveyId);
    }

    /**
     * Returns information if given survey has participant with given e-mail
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     * @return bool
     */
    public function hasParticipant($surveyId, $email)
    {
        return null !== $this->getParticipantDetails($surveyId, $email);
    }

    /**
     * Adds participant with given data to survey with given ID
     *
     * @param int    $surveyId  ID of survey
     * @param string $firstName First name of the participant to add
     * @param string $lastName  Last ame of the participant to add
     * @param string $email     E-mail address of the participant to add
     * @return Participant
     */
    public function addParticipant($surveyId, $firstName, $lastName, $email)
    {
        $participantsData = [
            [
                'firstname' => $firstName,
                'lastname'  => $lastName,
                'email'     => $email,
            ],
        ];

        $arguments = [
            $surveyId,
            $participantsData,
        ];

        $participantCollection = $this
            ->client
            ->run(MethodType::ADD_PARTICIPANTS, $arguments)
            ->getData();

        /* @var Participant $addedParticipant */
        $addedParticipant = $participantCollection->getFirst();

        $participants = new Collection([
            ParticipantShort::fromParticipant($addedParticipant),
        ]);

        $this
            ->allParticipants
            ->addParticipants($participants, $surveyId);

        return $participantCollection->getFirst();
    }

    /**
     * Returns short data of one participant with given e-mail (participant of given survey)
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     * @return ParticipantShort|null
     */
    public function getParticipant($surveyId, $email)
    {
        /*
         * I have to get all participants of survey to avoid problem when participants exist but are not loaded
         */
        $this->getSurveyParticipants($surveyId);

        $participant = $this
            ->allParticipants
            ->getParticipantOfSurvey($surveyId, $email);

        /* @var ParticipantShort $participant */
        return $participant;
    }

    /**
     * Returns full data of participant with given e-mail (participant of given survey)
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     * @return Participant|null
     *
     * @throws CannotProcessDataException
     */
    public function getParticipantDetails($surveyId, $email)
    {
        if (!$this->participantsDetails->hasParticipantOfSurvey($surveyId, $email)) {
            $participant = null;

            $arguments = [
                $surveyId,
                [
                    'email' => $email,
                ],
            ];

            try {
                /* @var Participant $participant */
                $participant = $this
                    ->client
                    ->run(MethodType::GET_PARTICIPANT_PROPERTIES, $arguments)
                    ->getData();
            } catch (CannotProcessDataException $exception) {
                /*
                 * Oops, something is broken, because the reason is different than "participant was not found"
                 */
                if (ReasonType::NO_PARTICIPANT_PROPERTIES !== $exception->getReason()) {
                    throw $exception;
                }
            }

            if (null !== $participant) {
                $this->participantsDetails->addParticipant($participant, $surveyId);
            }
        }

        $participant = $this
            ->participantsDetails
            ->getParticipantOfSurvey($surveyId, $email);

        return $participant;
    }

    /**
     * Returns information if participant with given e-mail has filled given survey
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     * @return bool
     *
     * @throws MissingParticipantOfSurveyException
     */
    public function hasParticipantFilledSurvey($surveyId, $email)
    {
        if ($this->hasParticipant($surveyId, $email)) {
            return true === $this
                    ->getParticipantDetails($surveyId, $email)
                    ->isCompleted();
        }

        throw new MissingParticipantOfSurveyException($surveyId, $email);
    }
}
