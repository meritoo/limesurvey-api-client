<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Service;

use Meritoo\Common\Collection\Collection;
use Meritoo\LimeSurvey\ApiClient\Client\Client;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Service that serves surveys
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveyService
{
    /**
     * Client of the LimeSurvey's API
     *
     * @var Client
     */
    private $client;

    /**
     * All surveys
     *
     * @var Collection
     */
    private $allSurveys;

    /**
     * Class constructor
     *
     * @param Client     $client     Client of the LimeSurvey's API
     * @param Collection $allSurveys (optional) All surveys
     */
    public function __construct(Client $client, Collection $allSurveys = null)
    {
        if (null === $allSurveys) {
            $allSurveys = new Collection();
        }

        $this->client = $client;
        $this->allSurveys = $allSurveys;
    }

    /**
     * Returns all surveys
     *
     * @return Collection
     */
    public function getAllSurveys()
    {
        if ($this->allSurveys->isEmpty()) {
            $surveys = $this
                ->client
                ->run(MethodType::LIST_SURVEYS)
                ->getData();

            if (null !== $surveys) {
                $this->allSurveys = $surveys;
            }
        }

        return $this->allSurveys;
    }

    /**
     * Returns information if survey with given ID exists
     *
     * @param int $surveyId ID of survey to verify
     * @return bool
     */
    public function isExistingSurvey($surveyId)
    {
        $allSurveys = $this->getAllSurveys();

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
}
