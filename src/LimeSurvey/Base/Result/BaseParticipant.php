<?php

namespace Meritoo\LimeSurvey\ApiClient\Base\Result;

/**
 * Base class for participant of survey.
 * Used as a foundation for short or full participant's data.
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseParticipant extends BaseItem
{
    /**
     * ID of the participant
     *
     * @var int
     */
    protected $id;

    /**
     * First name of the participant
     *
     * @var string
     */
    protected $firstName;

    /**
     * Last name of the participant
     *
     * @var string
     */
    protected $lastName;

    /**
     * E-mail of the participant
     *
     * @var string
     */
    protected $email;

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
}
