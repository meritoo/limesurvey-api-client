<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

use DateTime;
use Meritoo\Common\Utilities\Date;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * One item of the result/data: survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Survey extends BaseItem
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Result\Item\Survey';

    /**
     * ID of the survey
     *
     * @var int
     */
    private $id;

    /**
     * Title of the survey
     *
     * @var string
     */
    private $title;

    /**
     * Date when the survey starts
     *
     * @var DateTime
     */
    private $startsAt;

    /**
     * Date when the survey expires
     *
     * @var DateTime
     */
    private $expiresAt;

    /**
     * Information if the survey is active
     *
     * @var bool
     */
    private $active = false;

    /**
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        switch ($property) {
            case 'sid':
                $this->id = (int)$value;
                break;

            case 'surveyls_title':
                $this->title = trim($value);
                break;

            case 'startdate':
                if (null === $value) {
                    break;
                }

                $this->startsAt = Date::getDateTime($value, true);
                break;

            case 'expires':
                if (null === $value) {
                    break;
                }

                $this->expiresAt = Date::getDateTime($value, true);
                break;

            case 'active':
                $this->active = 'Y' === trim(strtoupper($value));
                break;
        }
    }

    /**
     * Returns ID of the survey
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns title of the survey
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns date when the survey starts
     *
     * @return DateTime|null
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * Returns date when the survey expires
     *
     * @return DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Returns information if the survey is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }
}
