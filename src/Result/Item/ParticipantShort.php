<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;

/**
 * One item of the result/data: short data of one participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantShort extends BaseItem
{
    /**
     * ID of the participant
     *
     * @var int
     */
    private $id;

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
     * {@inheritdoc}
     */
    public function setValue($property, $value)
    {
        switch ($property) {
            case 'tid':
                $this->id = (int)$value;
                break;

            case 'participant_info':
                $this->firstName = trim($value['firstname']);
                $this->lastName = trim($value['lastname']);
                $this->email = trim($value['email']);
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
     * Returns short data of participant created from full data of participant
     *
     * @param Participant $participant Full data of participant
     * @return $this
     */
    public static function fromParticipant(Participant $participant)
    {
        $info = [
            'firstname' => $participant->getFirstName(),
            'lastname'  => $participant->getLastName(),
            'email'     => $participant->getEmail(),
        ];

        $data = [
            'tid'              => $participant->getId(),
            'participant_info' => $info,
        ];

        return new self($data);
    }
}
