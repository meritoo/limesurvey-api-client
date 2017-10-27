<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * One item of the result/data: survey's summary (contains aggregated data)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SurveySummary extends BaseItem
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary';

    /**
     * Count/Amount of tokens
     *
     * @var int
     */
    private $tokenCount;

    /**
     * Count/Amount of invalid tokens
     *
     * @var int
     */
    private $tokenInvalidCount;

    /**
     * Count/Amount of sent tokens
     *
     * @var int
     */
    private $tokenSentCount;

    /**
     * Count/Amount of opted out tokens
     *
     * @var int
     */
    private $tokenOptedOutCount;

    /**
     * Count/Amount of completed tokens
     *
     * @var int
     */
    private $tokenCompletedCount;

    /**
     * Count/Amount of complete responses
     *
     * @var int
     */
    private $completeResponsesCount;

    /**
     * Count/Amount of incomplete responses
     *
     * @var int
     */
    private $incompleteResponsesCount;

    /**
     * Count/Amount of full responses
     *
     * @var int
     */
    private $fullResponsesCount;

    /**
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        switch ($property) {
            case 'token_count':
                $this->tokenCount = (int)$value;
                break;

            case 'token_invalid':
                $this->tokenInvalidCount = (int)$value;
                break;

            case 'token_sent':
                $this->tokenSentCount = (int)$value;
                break;

            case 'token_opted_out':
                $this->tokenOptedOutCount = (int)$value;
                break;

            case 'token_completed':
                $this->tokenCompletedCount = (int)$value;
                break;

            case 'completed_responses':
                $this->completeResponsesCount = (int)$value;
                break;

            case 'incomplete_responses':
                $this->incompleteResponsesCount = (int)$value;
                break;

            case 'full_responses':
                $this->fullResponsesCount = (int)$value;
                break;
        }
    }

    /**
     * Returns count/amount of tokens
     *
     * @return int
     */
    public function getTokenCount()
    {
        return $this->tokenCount;
    }

    /**
     * Returns count/amount of invalid tokens
     *
     * @return int
     */
    public function getTokenInvalidCount()
    {
        return $this->tokenInvalidCount;
    }

    /**
     * Returns count/amount of sent tokens
     *
     * @return int
     */
    public function getTokenSentCount()
    {
        return $this->tokenSentCount;
    }

    /**
     * Returns count/amount of opted out tokens
     *
     * @return int
     */
    public function getTokenOptedOutCount()
    {
        return $this->tokenOptedOutCount;
    }

    /**
     * Returns count/amount of completed tokens
     *
     * @return int
     */
    public function getTokenCompletedCount()
    {
        return $this->tokenCompletedCount;
    }

    /**
     * Returns count/amount of complete responses
     *
     * @return int
     */
    public function getCompleteResponsesCount()
    {
        return $this->completeResponsesCount;
    }

    /**
     * Returns count/amount of incomplete responses
     *
     * @return int
     */
    public function getIncompleteResponsesCount()
    {
        return $this->incompleteResponsesCount;
    }

    /**
     * Returns count/amount of full responses
     *
     * @return int
     */
    public function getFullResponsesCount()
    {
        return $this->fullResponsesCount;
    }
}
