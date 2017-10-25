<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Collection;

use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Exception\Method\DisabledMethodException;
use Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary;

/**
 * Collection of surveys' summaries (the SurveySummary class instances)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveysSummaries extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function add($element, $index = null)
    {
        throw new DisabledMethodException(__METHOD__, 'addSurveySummary');
    }

    /**
     * {@inheritdoc}
     */
    public function addMultiple($elements, $useIndexes = false)
    {
        throw new DisabledMethodException(__METHOD__, 'addSurveysSummaries');
    }

    /**
     * {@inheritdoc}
     */
    public function has($element)
    {
        throw new DisabledMethodException(__METHOD__, 'hasSurveySummary');
    }

    /**
     * Adds survey's summary
     *
     * @param SurveySummary $summary  Survey's summary
     * @param int           $surveyId ID of survey
     * @return $this
     */
    public function addSurveySummary(SurveySummary $summary, $surveyId)
    {
        $this[$surveyId] = $summary;

        return $this;
    }

    /**
     * Adds surveys' summaries
     *
     * @param array $summaries Surveys' summaries to add
     * @return $this
     */
    public function addSurveysSummaries(array $summaries)
    {
        /*
         * No summaries?
         * Nothing to do
         */
        if (empty($summaries)) {
            return $this;
        }

        foreach ($summaries as $surveyId => $summary) {
            $this->addSurveySummary($summary, $surveyId);
        }

        return $this;
    }

    /**
     * Returns information if there is summary of survey with given ID
     *
     * @param int $surveyId ID of survey
     * @return bool
     */
    public function hasSurveySummary($surveyId)
    {
        /*
         * There are no surveys' summaries or there is no summary of survey with given ID?
         */
        if ($this->isEmpty() || !isset($this[$surveyId])) {
            return false;
        }

        return true;
    }

    /**
     * Returns summary of survey with given ID
     *
     * @param int $surveyId ID of survey
     * @return SurveySummary|null
     */
    public function getSurveySummary($surveyId)
    {
        if ($this->hasSurveySummary($surveyId)) {
            return $this[$surveyId];
        }

        return null;
    }
}
