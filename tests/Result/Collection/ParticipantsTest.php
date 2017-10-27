<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Result\Collection;

use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Exception\Method\DisabledMethodException;
use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Result\Collection\Participants;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Participant;
use Meritoo\LimeSurvey\ApiClient\Result\Item\ParticipantShort;

/**
 * Test case of the collection of participants' short data
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantsTest extends BaseTestCase
{
    /**
     * An empty collection of participants' short data
     *
     * @var Participants
     */
    private $participantsEmpty;

    /**
     * Collection of participants of 1 survey
     *
     * @var Participants
     */
    private $participantsOfOneSurvey;

    /**
     * Collection of participants of more than 1 survey
     *
     * @var Participants
     */
    private $participantsOfManySurvey;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(Participants::className, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testAdd()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new Participants())->add('');
    }

    public function testAddMultiple()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new Participants())->addMultiple([]);
    }

    public function testHas()
    {
        $this->setExpectedException(DisabledMethodException::className);
        (new Participants())->has(new Participant());
    }

    public function testAddParticipantsUsingEmptyCollection()
    {
        $surveyId = 1;

        $participants = new Participants();
        $result = $participants->addParticipants(new Collection(), $surveyId);

        static::assertFalse($participants->hasParticipantsOfSurvey($surveyId));
        static::assertFalse($participants->hasParticipantsOfSurvey(2));

        static::assertEquals($participants, $result);
        static::assertCount(0, $participants->getBySurvey($surveyId));
    }

    public function testAddParticipantsFirstParticipants()
    {
        $surveyId = 1;

        $participantsData = new Collection([
            new Participant(),
            new Participant(),
        ]);

        $result = $this
            ->participantsEmpty
            ->addParticipants($participantsData, $surveyId);

        static::assertTrue($this->participantsEmpty->hasParticipantsOfSurvey($surveyId));
        static::assertFalse($this->participantsEmpty->hasParticipantsOfSurvey(2));

        static::assertEquals($this->participantsEmpty, $result);
        static::assertCount(2, $this->participantsEmpty->getBySurvey($surveyId));
    }

    public function testAddParticipantsMoreParticipants()
    {
        $surveyId = 2;

        $participantsData = new Collection([
            new Participant(),
            new Participant(),
        ]);

        $result = $this
            ->participantsOfOneSurvey
            ->addParticipants($participantsData, $surveyId);

        static::assertTrue($this->participantsOfOneSurvey->hasParticipantsOfSurvey($surveyId));
        static::assertFalse($this->participantsOfOneSurvey->hasParticipantsOfSurvey(3));

        static::assertEquals($this->participantsOfOneSurvey, $result);
        static::assertCount(2, $this->participantsOfOneSurvey->getBySurvey($surveyId));
    }

    public function testAddParticipantFirstParticipant()
    {
        $surveyId = 1;
        $email = 'john@scott.com';

        $participant = new ParticipantShort([
            'tid'              => 1,
            'participant_info' => [
                'firstname' => 'John',
                'lastname'  => 'Scott',
                'email'     => $email,
            ],
        ]);

        $participants = new Participants();
        $result = $participants->addParticipant($participant, $surveyId);

        static::assertEquals($participants, $result);
        static::assertEquals($participant, $participants->getParticipantOfSurvey($surveyId, $email));

        static::assertTrue($participants->hasParticipantsOfSurvey($surveyId));
        static::assertFalse($participants->hasParticipantsOfSurvey(2));
    }

    public function testAddParticipantNotFirstParticipant()
    {
        $surveyId = 1;
        $email = 'john@scott.com';

        $participant = new ParticipantShort([
            'tid'              => 1,
            'participant_info' => [
                'firstname' => 'John',
                'lastname'  => 'Scott',
                'email'     => $email,
            ],
        ]);

        $result = $this
            ->participantsOfOneSurvey
            ->addParticipant($participant, $surveyId);

        static::assertEquals($this->participantsOfOneSurvey, $result);
        static::assertEquals($participant, $this->participantsOfOneSurvey->getParticipantOfSurvey($surveyId, $email));

        static::assertTrue($this->participantsOfOneSurvey->hasParticipantsOfSurvey($surveyId));
        static::assertFalse($this->participantsOfOneSurvey->hasParticipantsOfSurvey(2));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->participantsEmpty = new Participants();
        $this->participantsOfOneSurvey = new Participants();
        $this->participantsOfManySurvey = new Participants();

        $participants1Survey = new Collection();
        $participants2Survey = new Collection();
        $participants3Survey = new Collection();

        $this
            ->participantsOfOneSurvey
            ->addParticipants($participants1Survey, 1);

        $this
            ->participantsOfManySurvey
            ->addParticipants($participants1Survey, 2)
            ->addParticipants($participants2Survey, 3)
            ->addParticipants($participants3Survey, 4);
    }
}
