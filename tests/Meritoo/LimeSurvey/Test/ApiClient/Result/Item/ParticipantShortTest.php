<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Item;

use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;
use PHPUnit_Framework_TestCase;

/**
 * Test case of the one item of the result/data: short data of participant
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantShortTest extends PHPUnit_Framework_TestCase
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
                'token'            => uniqid(),
                'participant_info' => [
                    'firstname' => 'Lorem',
                    'lastname'  => 'Ipsum',
                    'email'     => 'lorem@ipsum.com',
                ],
            ],
            [
                'tid'              => '456',
                'token'            => uniqid(),
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

        $this->participant1stInstance = (new ParticipantShort())->setValues($this->rawData[0]);
        $this->participant2ndInstance = (new ParticipantShort())->setValues($this->rawData[1]);
    }
}
