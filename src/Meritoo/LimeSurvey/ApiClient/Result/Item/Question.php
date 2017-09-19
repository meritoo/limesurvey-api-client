<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

/**
 * One item of the result/data: full data of one question of survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Question extends QuestionShort
{
    /**
     * Available answers
     *
     * @var array
     */
    private $availableAnswers;

    /**
     * Sub-questions
     * @var array
     */
    private $subQuestions;

    /**
     * Attributes
     *
     * @var array
     */
    private $attributes;

    /**
     * Attributes in languages
     *
     * @var array
     */
    private $attributesLanguages;

    /**
     * Answer's options
     *
     * @var array
     */
    private $answerOptions;

    /**
     * Default value
     *
     * @var string
     */
    private $defaultValue;

    /**
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        parent::setValue($property, $value);

        switch ($property) {
            case 'available_answers':
                $this->availableAnswers = $value;
                break;

            case 'subquestions':
                $this->subQuestions = $value;
                break;

            case 'attributes':
                $this->attributes = $value;
                break;

            case 'attributes_lang':
                $this->attributesLanguages = $value;
                break;

            case 'answeroptions':
                $this->answerOptions = $value;
                break;

            case 'defaultvalue':
                $this->defaultValue = $value;
                break;
        }
    }

    /**
     * Returns available answers
     *
     * @return array
     */
    public function getAvailableAnswers()
    {
        return $this->availableAnswers;
    }

    /**
     * Returns sub-questions
     *
     * @return array
     */
    public function getSubQuestions()
    {
        return $this->subQuestions;
    }

    /**
     * Returns attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns attributes in languages
     *
     * @return array
     */
    public function getAttributesLanguages()
    {
        return $this->attributesLanguages;
    }

    /**
     * Returns answer's options
     *
     * @return array
     */
    public function getAnswerOptions()
    {
        return $this->answerOptions;
    }

    /**
     * Returns default value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
