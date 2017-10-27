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
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Surveys;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use Meritoo\LimeSurvey\ApiClient\Type\ReasonType;

/**
 * Service that serves surveys
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
     * @param Client  $client     Client of the LimeSurvey's API
     * @param Surveys $allSurveys (optional) All surveys. Collection of surveys (the Survey class instances).
     */
    public function __construct(Client $client, Surveys $allSurveys = null)
    {
        if (null === $allSurveys) {
            $allSurveys = new Surveys();
        }

        $this->client = $client;
        $this->allSurveys = $allSurveys;
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
     * @param bool $onlyActive (optional) If is set to true, active surveys are returned only. Otherwise - all.
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
     *                             be returned, even if exists. Otherwise - it doesn't matter.
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
     * Returns url used to start survey for given survey and participant
     *
     * @param int         $surveyId    ID of survey to start
     * @param Participant $participant Participant who would like to start survey
     * @return string
     */
    public function getStartSurveyUrl($surveyId, Participant $participant)
    {
        $baseUrl = $this
            ->client
            ->getConfiguration()
            ->getBaseUrl();

        return sprintf($this->startSurveyUrlTemplate, $baseUrl, $surveyId, $participant->getToken());
    }
}