<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Processor;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownInstanceOfResultItem;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Question;
use Meritoo\LimeSurvey\ApiClient\Result\Item\QuestionShort;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;
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
     * @return null|BaseItem|array
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
         * Prepare instance of one item
         */
        $item = $this->getItemInstance($method);

        /*
         * The raw data is or, actually, should be iterable?
         */
        if (MethodType::isResultIterable($method)) {
            $items = [];
            $emptyItem = clone $item;

            foreach ($rawData as $itemData) {
                $items[] = $emptyItem->setValues($itemData);
            }

            return $items;
        }

        return $item->setValues($rawData);
    }

    /**
     * Returns instance of one item of the result
     *
     * @param string $method  Name of called method while talking to the LimeSurvey's API. One of the MethodType
     *                        class constants.
     * @return BaseItem
     * @throws UnknownInstanceOfResultItem
     */
    private function getItemInstance($method)
    {
        $item = null;
        $method = MethodType::getValidatedMethod($method);

        switch ($method) {
            case MethodType::ADD_PARTICIPANTS:
            case MethodType::GET_PARTICIPANT_PROPERTIES:
                $item = new Participant();
                break;

            case MethodType::GET_QUESTION_PROPERTIES:
                $item = new Question();
                break;

            case MethodType::LIST_PARTICIPANTS:
                $item = new ParticipantShort();
                break;

            case MethodType::LIST_QUESTIONS:
                $item = new QuestionShort();
                break;

            case MethodType::LIST_SURVEYS:
                $item = new Survey();
                break;

            /*
             * todo: Use other types of methods and create proper classes (used to get instances of one item)
             */
        }

        /*
         * Instance of the item is unknown?
         */
        if (null === $item) {
            throw new UnknownInstanceOfResultItem($method);
        }

        return $item;
    }
}
