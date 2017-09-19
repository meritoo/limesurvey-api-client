<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use DateTime;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use PHPUnit_Framework_TestCase;

/**
 * Test case of the one item of the result/data: full data of participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantTest extends PHPUnit_Framework_TestCase
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
     * @var Participant
     */
    private $participant1stInstance;

    /**
     * 2nd instance of the participant created using the raw data
     *
     * @var Participant
     */
    private $participant2ndInstance;

    public function testCreateOfTheParticipant()
    {
        $processor = new ResultProcessor();
        $processed = $processor->process(MethodType::GET_PARTICIPANT_PROPERTIES, $this->rawData[0]);

        static::assertInstanceOf(Participant::class, $processed);
    }

    public function testGetId()
    {
        static::assertEquals(123, $this->participant1stInstance->getId());
        static::assertEquals(456, $this->participant2ndInstance->getId());
    }

    public function testGetParticipantId()
    {
        static::assertEquals(0, $this->participant1stInstance->getParticipantId());
        static::assertEquals(789, $this->participant2ndInstance->getParticipantId());
    }

    public function testGetMpId()
    {
        static::assertEquals(0, $this->participant1stInstance->getMpId());
        static::assertEquals(1, $this->participant2ndInstance->getMpId());
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

    public function testGetEmailStatus()
    {
        static::assertEquals('OK', $this->participant1stInstance->getEmailStatus());
        static::assertEquals('OK', $this->participant2ndInstance->getEmailStatus());
    }

    public function testGetToken()
    {
        static::assertEquals($this->rawData[0]['token'], $this->participant1stInstance->getToken());
        static::assertEquals($this->rawData[1]['token'], $this->participant2ndInstance->getToken());
    }

    public function testGetLanguage()
    {
        static::assertEquals('pl', $this->participant1stInstance->getLanguage());
        static::assertEquals('en', $this->participant2ndInstance->getLanguage());
    }

    public function testIsBlacklisted()
    {
        static::assertFalse($this->participant1stInstance->isBlacklisted());
        static::assertTrue($this->participant2ndInstance->isBlacklisted());
    }

    public function testIsSent()
    {
        static::assertTrue($this->participant1stInstance->isSent());
        static::assertTrue($this->participant2ndInstance->isSent());
    }

    public function testIsReminderSent()
    {
        static::assertFalse($this->participant1stInstance->isReminderSent());
        static::assertFalse($this->participant2ndInstance->isReminderSent());
    }

    public function testGetReminderCount()
    {
        static::assertEquals(0, $this->participant1stInstance->getReminderCount());
        static::assertEquals(1, $this->participant2ndInstance->getReminderCount());
    }

    public function testIsCompleted()
    {
        static::assertFalse($this->participant1stInstance->isCompleted());
        static::assertTrue($this->participant2ndInstance->isCompleted());
    }

    public function testGetUsesLeft()
    {
        static::assertEquals(10, $this->participant1stInstance->getUsesLeft());
        static::assertEquals(5, $this->participant2ndInstance->getUsesLeft());
    }

    public function testGetValidFrom()
    {
        static::assertNull($this->participant1stInstance->getValidFrom());
        static::assertEquals(new DateTime($this->rawData[1]['validfrom']), $this->participant2ndInstance->getValidFrom());
    }

    public function testGetValidUntil()
    {
        static::assertEquals(new DateTime($this->rawData[0]['validuntil']), $this->participant1stInstance->getValidUntil());
        static::assertNull($this->participant2ndInstance->getValidUntil());
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
                'tid'            => '123',
                'participant_id' => null,
                'mpid'           => null,
                'firstname'      => 'Lorem',
                'lastname'       => 'Ipsum',
                'email'          => 'lorem@ipsum.com',
                'emailstatus'    => 'OK',
                'token'          => uniqid(),
                'language'       => 'pl',
                'blacklisted'    => 'N',
                'sent'           => 'Y',
                'remindersent'   => 'N',
                'remindercount'  => 0,
                'completed'      => 'N',
                'usesleft'       => 10,
                'validfrom'      => null,
                'validuntil'     => (new DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'tid'            => '456',
                'participant_id' => '789',
                'mpid'           => '001',
                'firstname'      => 'Dolor',
                'lastname'       => 'Sit',
                'email'          => 'dolor@sit.com',
                'emailstatus'    => 'OK',
                'token'          => uniqid(),
                'language'       => 'en',
                'blacklisted'    => 'Y',
                'sent'           => 'Y',
                'remindersent'   => 'N',
                'remindercount'  => 1,
                'completed'      => 'Y',
                'usesleft'       => 5,
                'validfrom'      => (new DateTime())->format('Y-m-d H:i:s'),
                'validuntil'     => null,
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

        $this->participant1stInstance = (new Participant())->setValues($this->rawData[0]);
        $this->participant2ndInstance = (new Participant())->setValues($this->rawData[1]);
    }
}
