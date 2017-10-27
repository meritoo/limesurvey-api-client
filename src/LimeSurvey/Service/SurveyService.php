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
use Meritoo\LimeSurvey\ApiClient\Exception\MissingSurveySummaryException;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Surveys;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\SurveysSummaries;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;

/**
 * Service that serves surveys and participants of surveys
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveyService
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Service\SurveyService';

    /**
     * Client of the LimeSurvey's API
     *
     * @var Client
     */
    private $client;

    /**
     * All surveys.
     * Collection of surveys (the Survey class instances).
     *
     * @var Surveys
     */
    private $allSurveys;

    /**
     * Collection of participants' short data.
     * All participants grouped per survey.
     *
     * @var Participants
     */
    private $allParticipants;

    /**
     * Collection of surveys' summaries (the SurveySummary class instances)
     *
     * @var SurveysSummaries
     */
    private $surveySummaries;

    /**
     * Template of the url used to start survey
     *
     * Example:
     * - url: https://your.limesurvey.instance/12345?token=q1w2e3r4t5y6
     * - LimeSurvey frontend: https://your.limesurvey.instance
     * - survey ID: 12345
     * - token: q1w2e3r4t5y6
     *
     * @var string
     */
    private $startSurveyUrlTemplate = '%s/%d?token=%s';

    /**
     * Class constructor
     *
     * @param Client           $client           Client of the LimeSurvey's API
     * @param Surveys          $allSurveys       (optional) All surveys. Collection of surveys (the Survey class
     *                                           instances).
     * @param Participants     $allParticipants  (optional) Collection of participants' short data. All participants
     *                                           grouped per survey.
     * @param SurveysSummaries $surveysSummaries (optional) Collection of surveys' summaries (the SurveySummary class
     *                                           instances)
     */
    public function __construct(
        Client $client,
        Surveys $allSurveys = null,
        Participants $allParticipants = null,
        SurveysSummaries $surveysSummaries = null
    ) {
        if (null === $allSurveys) {
            $allSurveys = new Surveys();
        }

        if (null === $allParticipants) {
            $allParticipants = new Participants();
        }

        if (null === $surveysSummaries) {
            $surveysSummaries = new SurveysSummaries();
        }

        $this->client = $client;
        $this->allSurveys = $allSurveys;
        $this->allParticipants = $allParticipants;
        $this->surveySummaries = $surveysSummaries;
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
     * Returns all surveys
     *
     * @param bool $onlyActive (optional) If is set to true, active surveys are returned only. Otherwise - all (default
     *                         behaviour).
     * @return Surveys
     *
     * @throws CannotProcessDataException
     */
    public function getAllSurveys($onlyActive = false)
    {
        if ($this->allSurveys->isEmpty()) {
            $surveys = new Surveys();

            try {
                $surveys = $this
                    ->client
                    ->run(MethodType::LIST_SURVEYS)
                    ->getData();
            } catch (CannotProcessDataException $exception) {
                $reason = $exception->getReason();

                /*
                 * Reason of the exception is different than "Oops, there is no surveys. Everything else is fine."?
                 * Let's throw the exception
                 */
                if (ReasonType::NO_SURVEYS_FOUND !== $reason) {
                    throw $exception;
                }
            }

            if (null !== $surveys && $surveys instanceof Collection) {
                $this->allSurveys = new Surveys($surveys->toArray());
            }
        }

        return $this->allSurveys->getAll($onlyActive);
    }

    /**
     * Returns information if survey with given ID exists
     *
     * @param int  $surveyId       ID of survey to verify
     * @param bool $shouldBeActive (optional) If is set to true, survey should be active. If it's not, it shouldn't
     *                             be returned, even if exists. Otherwise - it doesn't matter (default behaviour).
     * @return bool
     */
    public function isExistingSurvey($surveyId, $shouldBeActive = false)
    {
        $allSurveys = $this->getAllSurveys($shouldBeActive);

        /*
         * No surveys?
         * Nothing to do
         */
        if ($allSurveys->isEmpty()) {
            return false;
        }

        $surveyId = (int)$surveyId;

        /* @var Survey $survey */
        foreach ($allSurveys as $survey) {
            if ($survey->getId() == $surveyId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns url used to start survey for given survey and participant's token
     *
     * @param int    $surveyId         ID of survey to start
     * @param string $participantToken Token of participant who would like to start survey
     * @return string
     */
    public function getStartSurveyUrlByToken($surveyId, $participantToken)
    {
        $baseUrl = $this
            ->client
            ->getConfiguration()
            ->getBaseUrl();

        return sprintf($this->startSurveyUrlTemplate, $baseUrl, $surveyId, $participantToken);
    }

    /**
     * Returns url used to start survey for given survey and participant
     *
     * @param int         $surveyId    ID of survey to start
     * @param Participant $participant Participant who would like to start survey
     * @return string
     */
    public function getStartSurveyUrl($surveyId, Participant $participant)
    {
        return $this->getStartSurveyUrlByToken($surveyId, $participant->getToken());
    }

    /**
     * Returns participants of given survey
     *
     * @param int  $surveyId      ID of survey
     * @param bool $onlyCompleted (optional) If is set to true, participants who completed survey are returned only.
     *                            Otherwise - all (default behaviour).
     * @return Collection
     *
     * @throws CannotProcessDataException
     */
    public function getSurveyParticipants($surveyId, $onlyCompleted = false)
    {
        $hasSurvey = $this
            ->allParticipants
            ->hasParticipantsOfSurvey($surveyId);

        if (!$hasSurvey) {
            $offset = 0;
            $limit = $this->getSurveyTokenCount($surveyId);
            $includeUnused = !$onlyCompleted;

            $arguments = [
                $surveyId,
                $offset,
                $limit,
                $includeUnused,
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
     * Returns count/amount of tokens of survey with given ID
     *
     * @param int $surveyId ID of survey
     * @return int
     *
     * @throws MissingSurveySummaryException
     */
    public function getSurveyTokenCount($surveyId)
    {
        $surveySummary = $this
            ->surveySummaries
            ->getSurveySummary($surveyId);

        /*
         * Unknown survey's summary?
         * Let's fetch it
         */
        if (null === $surveySummary) {
            $surveySummary = $this->getSurveySummary($surveyId);
        }

        /*
         * Oops, survey's summary is missing
         */
        if (null === $surveySummary) {
            throw new MissingSurveySummaryException($surveyId);
        }

        return $surveySummary->getTokenCount();
    }

    /**
     * Returns summary of survey with given ID
     *
     * @param int $surveyId ID of survey
     * @return SurveySummary|null
     */
    private function getSurveySummary($surveyId)
    {
        $arguments = [
            $surveyId,
        ];

        /* @var SurveySummary $surveySummary */
        $surveySummary = $this
            ->client
            ->run(MethodType::GET_SUMMARY, $arguments)
            ->getData();

        if (null !== $surveySummary) {
            $this
                ->surveySummaries
                ->addSurveySummary($surveySummary, $surveyId);
        }

        return $surveySummary;
    }
}
