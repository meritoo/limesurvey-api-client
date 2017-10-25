<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Processor;

use Meritoo\Common\Utilities\Reflection;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\IncorrectClassOfResultItemException;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Question;
use Meritoo\LimeSurvey\ApiClient\Result\Item\QuestionShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
use Meritoo\LimeSurvey\ApiClient\Result\Item\SurveySummary;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Processor of the raw data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ResultProcessor
{
    /**
     * Returns processed data based on the raw data returned by the LimeSurvey's API
     *
     * @param string $method    Name of called method while talking to the LimeSurvey's API. One of the MethodType
     *                          class constants.
     * @param array  $rawData   Data returned by the LimeSurvey's API
     * @return array|BaseItem|null
     *
     * @throws IncorrectClassOfResultItemException
     */
    public function process($method, array $rawData)
    {
        $method = MethodType::getValidatedMethod($method);

        /*
         * No data?
         * Nothing to do
         */
        if (empty($rawData)) {
            return null;
        }

        /*
         * Prepare class name for instance of one item
         */
        $itemClassName = $this->getItemClassName($method);

        /*
         * The raw data is or, actually, should be iterable?
         */
        if (MethodType::isResultIterable($method)) {
            $items = [];

            foreach ($rawData as $itemData) {
                $items[] = new $itemClassName($itemData);
            }

            return $items;
        }

        return new $itemClassName($rawData);
    }

    /**
     * Returns class name used to create instance of one item of the result
     *
     * @param string $method  Name of called method while talking to the LimeSurvey's API. One of the MethodType
     *                        class constants.
     * @return string
     *
     * @throws IncorrectClassOfResultItemException
     * @throws UnknownInstanceOfResultItem
     */
    private function getItemClassName($method)
    {
        $className = null;
        $method = MethodType::getValidatedMethod($method);

        switch ($method) {
            case MethodType::ADD_PARTICIPANTS:
            case MethodType::GET_PARTICIPANT_PROPERTIES:
                $className = Participant::class;
                break;

            case MethodType::GET_QUESTION_PROPERTIES:
                $className = Question::class;
                break;

            case MethodType::GET_SUMMARY:
                $className = SurveySummary::class;
                break;

            case MethodType::LIST_PARTICIPANTS:
                $className = ParticipantShort::class;
                break;

            case MethodType::LIST_QUESTIONS:
                $className = QuestionShort::class;
                break;

            case MethodType::LIST_SURVEYS:
                $className = Survey::class;
                break;

            /*
             * todo: Use other types of methods and create proper classes (used to get instances of one item)
             */
        }

        /*
         * Oops, class name for instance of the item is unknown
         */
        if (null === $className) {
            throw new UnknownInstanceOfResultItem($method);
        }

        if (Reflection::isChildOfClass($className, BaseItem::class)) {
            return $className;
        }

        /*
         * Oops, class is incorrect (should extend BaseItem)
         */
        throw new IncorrectClassOfResultItemException($className);
    }
}
