<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Service;

use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
use Meritoo\LimeSurvey\ApiClient\Exception\MissingParticipantOfSurveyException;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\ParticipantsDetails;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
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
    const className = 'Meritoo\LimeSurvey\ApiClient\Service\ParticipantService';

    /**
     * Client of the LimeSurvey's API
     *
     * @var Client
     */
    private $client;

    /**
     * Collection of participants' full data.
     * All participants grouped per survey.
     *
     * @var ParticipantsDetails
     */
    private $participantsDetails;

    /**
     * Class constructor
     *
     * @param Client              $client               Client of the LimeSurvey's API
     * @param ParticipantsDetails $participantsDetails  (optional) Collection of participants' full data. All
     *                                                  participants grouped per survey.
     */
    public function __construct(
        Client $client,
        ParticipantsDetails $participantsDetails = null
    ) {
        if (null === $participantsDetails) {
            $participantsDetails = new ParticipantsDetails();
        }

        $this->client = $client;
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
