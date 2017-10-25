<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Item;

use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseParticipant;

/**
 * One item of the result/data: short data of one participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantShort extends BaseParticipant
{
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
