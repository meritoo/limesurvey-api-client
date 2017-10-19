<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Test case of the one item of the result/data: short data of participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantShortTest extends BaseTestCase
{
    /**
     * Raw data of participants
     *
     * @var array
     */
    private $rawData;

    /**
     * 1st instance of the participant created using the raw data
     *
     * @var ParticipantShort
     */
    private $participant1stInstance;

    /**
     * 2nd instance of the participant created using the raw data
     *
     * @var ParticipantShort
     */
    private $participant2ndInstance;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(ParticipantShort::className, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testCreateOfTheParticipant()
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::LIST_PARTICIPANTS, $this->rawData);

        static::assertCount(2, $processed);
    }

    public function testGetId()
    {
        static::assertEquals(123, $this->participant1stInstance->getId());
        static::assertEquals(456, $this->participant2ndInstance->getId());
    }

    public function testGetFirstName()
    {
        static::assertEquals('Lorem', $this->participant1stInstance->getFirstName());
        static::assertEquals('Dolor', $this->participant2ndInstance->getFirstName());
    }

    public function testGetLastName()
    {
        static::assertEquals('Ipsum', $this->participant1stInstance->getLastName());
        static::assertEquals('Sit', $this->participant2ndInstance->getLastName());
    }

    public function testGetEmail()
    {
        static::assertEquals('lorem@ipsum.com', $this->participant1stInstance->getEmail());
        static::assertEquals('dolor@sit.com', $this->participant2ndInstance->getEmail());
    }

    public function testFromParticipantUsingEmptyParticipant()
    {
        $participant = new Participant();
        $participantShort = ParticipantShort::fromParticipant($participant);

        static::assertEquals(0, $participantShort->getId());
        static::assertEquals('', $participantShort->getFirstName());
        static::assertEquals('', $participantShort->getLastName());
        static::assertEquals('', $participantShort->getEmail());

        static::assertEquals($participant->getId(), $participantShort->getId());
        static::assertEquals($participant->getFirstName(), $participantShort->getFirstName());
        static::assertEquals($participant->getLastName(), $participantShort->getLastName());
        static::assertEquals($participant->getEmail(), $participantShort->getEmail());
    }

    public function testFromParticipant()
    {
        $participant1 = new Participant([
            'tid'       => $this->rawData[0]['tid'],
            'firstname' => $this->rawData[0]['participant_info']['firstname'],
            'lastname'  => $this->rawData[0]['participant_info']['lastname'],
            'email'     => $this->rawData[0]['participant_info']['email'],
        ]);

        $participant2 = new Participant([
            'tid'       => $this->rawData[1]['tid'],
            'firstname' => $this->rawData[1]['participant_info']['firstname'],
            'lastname'  => $this->rawData[1]['participant_info']['lastname'],
            'email'     => $this->rawData[1]['participant_info']['email'],
        ]);

        $participantShort1 = ParticipantShort::fromParticipant($participant1);
        $participantShort2 = ParticipantShort::fromParticipant($participant2);

        static::assertEquals($participant1->getId(), $participantShort1->getId());
        static::assertEquals($participant1->getFirstName(), $participantShort1->getFirstName());
        static::assertEquals($participant1->getLastName(), $participantShort1->getLastName());
        static::assertEquals($participant1->getEmail(), $participantShort1->getEmail());

        static::assertEquals($participant2->getId(), $participantShort2->getId());
        static::assertEquals($participant2->getFirstName(), $participantShort2->getFirstName());
        static::assertEquals($participant2->getLastName(), $participantShort2->getLastName());
        static::assertEquals($participant2->getEmail(), $participantShort2->getEmail());
    }

    /**
     * Returns raw data of participants
     *
     * @return array
     */
    public static function getParticipantsRawData()
    {
        return [
            [
                'tid'              => '123',
                'participant_info' => [
                    'firstname' => 'Lorem',
                    'lastname'  => 'Ipsum',
                    'email'     => 'lorem@ipsum.com',
                ],
            ],
            [
                'tid'              => '456',
                'participant_info' => [
                    'firstname' => 'Dolor',
                    'lastname'  => 'Sit',
                    'email'     => 'dolor@sit.com',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->rawData = static::getParticipantsRawData();

        $this->participant1stInstance = new ParticipantShort($this->rawData[0]);
        $this->participant2ndInstance = new ParticipantShort($this->rawData[1]);
    }
}
