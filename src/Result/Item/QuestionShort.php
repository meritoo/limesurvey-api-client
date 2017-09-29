<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * One item of the result/data: short data of one question of survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class QuestionShort extends BaseItem
{
    /**
     * ID of the question
     *
     * @var int
     */
    private $id;

    /**
     * ID of the parent question
     *
     * @var int
     */
    private $parentId;

    /**
     * ID of the survey
     *
     * @var int
     */
    private $surveyId;

    /**
     * ID of the group of questions
     *
     * @var int
     */
    private $groupId;

    /**
     * ID of the scale
     * (what does it mean?)
     *
     * @var int
     */
    private $scaleId;

    /**
     * Type of the question
     *
     * @var string
     */
    private $type;

    /**
     * Title of the question
     *
     * @var string
     */
    private $title;

    /**
     * Content of the question
     *
     * @var string
     */
    private $content;

    /**
     * Explanation of the question
     *
     * @var string
     */
    private $contentHelp;

    /**
     * Regular expression
     * (what does it mean? used to validate answer?)
     *
     * @var string
     */
    private $regularExpression;

    /**
     * Information if type of question is other
     * (what does it mean?)
     *
     * @var bool
     */
    private $other;

    /**
     * Information if the question mandatory
     *
     * @var bool
     */
    private $mandatory;

    /**
     * Position/Order of the question
     *
     * @var int
     */
    private $position;

    /**
     * Language of the question
     *
     * @var string
     */
    private $language;

    /**
     * Same as default
     * (what does it mean?)
     *
     * @var int
     */
    private $sameDefault;

    /**
     * Relevant equation
     *
     * @var string
     */
    private $relevance;

    /**
     * Name of module
     * (what does it mean?)
     *
     * @var string
     */
    private $moduleName;

    /**
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        switch ($property) {
            case 'qid':
                $this->id = (int)$value;
                break;

            case 'parent_qid':
                $this->parentId = (int)$value;
                break;

            case 'sid':
                $this->surveyId = (int)$value;
                break;

            case 'gid':
                $this->groupId = (int)$value;
                break;

            case 'scale_id':
                $this->scaleId = (int)$value;
                break;

            case 'type':
                $this->type = trim($value);
                break;

            case 'title':
                $this->title = trim($value);
                break;

            case 'question':
                $this->content = trim($value);
                break;

            case 'help':
                $this->contentHelp = trim($value);
                break;

            case 'preg':
                if (null === $value) {
                    break;
                }

                $this->regularExpression = trim($value);
                break;

            case 'other':
                $this->other = 'Y' === trim(strtoupper($value));
                break;

            case 'mandatory':
                $this->mandatory = 'Y' === trim(strtoupper($value));
                break;

            case 'question_order':
                $this->position = (int)$value;
                break;

            case 'language':
                $this->language = trim($value);
                break;

            case 'same_default':
                $this->sameDefault = (int)$value;
                break;

            case 'relevance':
                $this->relevance = trim($value);
                break;

            case 'modulename':
                if (null === $value) {
                    break;
                }

                $this->moduleName = trim($value);
                break;
        }
    }

    /**
     * Returns ID of the question
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns ID of the parent question
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Returns ID of the survey
     *
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Returns ID of the group of questions
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Returns ID of the scale
     *
     * @return int
     */
    public function getScaleId()
    {
        return $this->scaleId;
    }

    /**
     * Returns type of the question
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns title of the question
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns content of the question
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns explanation of the question
     *
     * @return string
     */
    public function getContentHelp()
    {
        return $this->contentHelp;
    }

    /**
     * Returns regular expression
     *
     * @return string
     */
    public function getRegularExpression()
    {
        return $this->regularExpression;
    }

    /**
     * Returns information if type of question is other
     *
     * @return bool
     */
    public function isOther()
    {
        return $this->other;
    }

    /**
     * Returns information if the question mandatory
     *
     * @return bool
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Returns position/Order of the question
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns language of the question
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns information related to same as default
     *
     * @return int
     */
    public function getSameDefault()
    {
        return $this->sameDefault;
    }

    /**
     * Returns relevant equation
     *
     * @return string
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * Returns name of module
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
