<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Utilities;

use DateTime;

/**
 * A date's period.
 * Contains start and end date of the period.
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class DatePeriod
{
    /**
     * The period constant: last month
     *
     * @var int
     */
    const LAST_MONTH = 4;

    /**
     * The period constant: last week
     *
     * @var int
     */
    const LAST_WEEK = 1;

    /**
     * The period constant: last year
     *
     * @var int
     */
    const LAST_YEAR = 7;

    /**
     * The period constant: next month
     *
     * @var int
     */
    const NEXT_MONTH = 6;

    /**
     * The period constant: next week
     *
     * @var int
     */
    const NEXT_WEEK = 3;

    /**
     * The period constant: next year
     *
     * @var int
     */
    const NEXT_YEAR = 9;

    /**
     * The period constant: this month
     *
     * @var int
     */
    const THIS_MONTH = 5;

    /**
     * The period constant: this week
     *
     * @var int
     */
    const THIS_WEEK = 2;

    /**
     * The period constant: this year
     *
     * @var int
     */
    const THIS_YEAR = 8;

    /**
     * The start date of period
     *
     * @var DateTime
     */
    private $startDate;

    /**
     * The end date of period
     *
     * @var DateTime
     */
    private $endDate;

    /**
     * Class constructor
     *
     * @param DateTime $startDate (optional) The start date of period
     * @param DateTime $endDate   (optional) The end date of period
     */
    public function __construct(DateTime $startDate = null, DateTime $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Returns information if given period is correct
     *
     * @param int $period The period to verify
     * @return bool
     */
    public static function isCorrectPeriod($period)
    {
        return in_array($period, Reflection::getConstants(__CLASS__));
    }

    /**
     * Returns formatted one of the period's date: start date or end date
     *
     * @param string $format    Format used to format the date
     * @param bool   $startDate (optional) If is set to true, start date is formatted. Otherwise - end date.
     * @return string
     */
    public function getFormattedDate($format, $startDate = true)
    {
        $date = $this->getEndDate();

        /*
         * Start date should be formatted?
         */
        if ($startDate) {
            $date = $this->getStartDate();
        }

        /*
         * Unknown date or format is invalid?
         */
        if (null === $date || !Date::isValidDateFormat($format)) {
            return '';
        }

        return $date->format($format);
    }

    /**
     * Returns the end date of period
     *
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the end date of period
     *
     * @param DateTime $endDate (optional) The end date of period
     * @return $this
     */
    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Returns the start date of period
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the start date of period
     *
     * @param DateTime $startDate (optional) The start date of period
     * @return $this
     */
    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;

        return $this;
    }
}
