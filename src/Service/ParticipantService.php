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
    /**
     * Client of the LimeSurvey's API
     *
     * @var Client
     */
    private $client;

    /**
     * Participants of survey.
     * All participants grouped per survey.
     *
     * @var Participants
     */
    private $allParticipants;

    /**
     * Class constructor
     *
     * @param Client       $client          Client of the LimeSurvey's API
     * @param Participants $allParticipants (optional) Participants of survey. All participants grouped per survey.
     */
    public function __construct(Client $client, Participants $allParticipants = null)
    {
        if (null === $allParticipants) {
            $allParticipants = new Participants();
        }

        $this->client = $client;
        $this->allParticipants = $allParticipants;
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
                $reason = $exception->getReason();

                /*
                 * Reason of the exception is different than "Oops, there is no participants. Everything else is fine."?
                 * Let's throw the exception
                 */
                if (ReasonType::NO_PARTICIPANTS_FOUND !== $reason) {
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
        /*
         * I have to get all participants of survey to avoid problem when participants exist but are not loaded
         */
        $this->getSurveyParticipants($surveyId);

        return null !== $this
                ->allParticipants
                ->getParticipantOfSurvey($surveyId, $email);
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

        $this
            ->allParticipants
            ->addParticipants($participantCollection, $surveyId);

        return $participantCollection->getFirst();
    }

    /**
     * Returns participant with given e-mail of given survey
     *
     * @param int    $surveyId ID of survey
     * @param string $email    E-mail address of the participant
     * @return Participant|null
     */
    public function getParticipant($surveyId, $email)
    {
        /*
         * I have to get all participants of survey to avoid problem when participants exist but are not loaded
         */
        $this->getSurveyParticipants($surveyId);

        return $this
            ->allParticipants
            ->getParticipantOfSurvey($surveyId, $email);
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
                    ->getParticipant($surveyId, $email)
                    ->isCompleted();
        }

        throw new MissingParticipantOfSurveyException($surveyId, $email);
    }
}
