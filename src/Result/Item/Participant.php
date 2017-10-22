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
 * One item of the result/data: full data of one participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Participant extends BaseItem
{
    /**
     * ID of the participant
     *
     * @var int
     */
    private $id;

    /**
     * Another ID of the participant?
     * Don't know where it is used.
     *
     * @var int
     */
    private $participantId;

    /**
     * MP ID
     *
     * @var int
     */
    private $mpId;

    /**
     * First name of the participant
     *
     * @var string
     */
    private $firstName;

    /**
     * Last name of the participant
     *
     * @var string
     */
    private $lastName;

    /**
     * E-mail of the participant
     *
     * @var string
     */
    private $email;

    /**
     * Status of the e-mail
     *
     * @var string
     */
    private $emailStatus;

    /**
     * Token of the participant
     *
     * @var string
     */
    private $token;

    /**
     * Language of the participant
     *
     * @var string
     */
    private $language;

    /**
     * Information if participant is blacklisted
     *
     * @var bool
     */
    private $blacklisted;

    /**
     * Information if token was sent to participant/user
     *
     * @var bool
     */
    private $sent;

    /**
     * Information if reminder about token was sent to participant/user
     *
     * @var bool
     */
    private $reminderSent;

    /**
     * Count of sent reminders to participant/user
     *
     * @var int
     */
    private $reminderCount;

    /**
     * Information if the participant has completed the survey
     *
     * @var bool
     */
    private $completed;

    /**
     * Count of left uses of the token
     *
     * @var int
     */
    private $usesLeft;

    /**
     * Information from which day the token is valid
     *
     * @var DateTime
     */
    private $validFrom;

    /**
     * Information until which day the token is valid
     *
     * @var DateTime
     */
    private $validUntil;

    /**
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        switch ($property) {
            case 'tid':
                $this->id = (int)$value;
                break;

            case 'participant_id':
                $this->participantId = (int)$value;
                break;

            case 'mpid':
                $this->mpId = (int)$value;
                break;

            case 'firstname':
                $this->firstName = trim($value);
                break;

            case 'lastname':
                $this->lastName = trim($value);
                break;

            case 'email':
                $this->email = trim($value);
                break;

            case 'emailstatus':
                $this->emailStatus = trim($value);
                break;

            case 'token':
                $this->token = trim($value);
                break;

            case 'language':
                $this->language = trim($value);
                break;

            case 'blacklisted':
                $this->blacklisted = 'Y' === trim(strtoupper($value));
                break;

            case 'sent':
                $this->sent = 'Y' === trim(strtoupper($value));
                break;

            case 'remindersent':
                $this->reminderSent = 'Y' === trim(strtoupper($value));
                break;

            case 'remindercount':
                $this->reminderCount = (int)$value;
                break;

            case 'completed':
                if ('N' === trim(strtoupper($value))) {
                    $this->completed = false;
                    break;
                }

                $this->completed = Date::isValidDate($value, true);
                break;

            case 'usesleft':
                $this->usesLeft = (int)$value;
                break;

            case 'validfrom':
                if (null === $value) {
                    break;
                }

                $this->validFrom = Date::getDateTime($value, false, 'Y-m-d H:i:s');
                break;

            case 'validuntil':
                if (null === $value) {
                    break;
                }

                $this->validUntil = Date::getDateTime($value, false, 'Y-m-d H:i:s');
                break;
        }
    }

    /**
     * Returns ID of the participant
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns another ID of the participant?
     * Don't know where it is used.
     *
     * @return int
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }

    /**
     * Returns MP ID
     *
     * @return int
     */
    public function getMpId()
    {
        return $this->mpId;
    }

    /**
     * Returns first name of the participant
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Returns last name of the participant
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Returns e-mail of the participant
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns status of the e-mail
     *
     * @return string
     */
    public function getEmailStatus()
    {
        return $this->emailStatus;
    }

    /**
     * Returns token of the participant
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns language of the participant
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns information if participant is blacklisted
     *
     * @return bool
     */
    public function isBlacklisted()
    {
        return $this->blacklisted;
    }

    /**
     * Returns information if token was sent to participant/user
     *
     * @return bool
     */
    public function isSent()
    {
        return $this->sent;
    }

    /**
     * Returns information if reminder about token was sent to participant/user
     *
     * @return bool
     */
    public function isReminderSent()
    {
        return $this->reminderSent;
    }

    /**
     * Returns count of sent reminders to participant/user
     *
     * @return int
     */
    public function getReminderCount()
    {
        return $this->reminderCount;
    }

    /**
     * Returns information if the participant has completed the survey
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * Returns count of left uses of the token
     *
     * @return int
     */
    public function getUsesLeft()
    {
        return $this->usesLeft;
    }

    /**
     * Returns information from which day the token is valid
     *
     * @return DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Returns information until which day the token is valid
     *
     * @return DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }
}
