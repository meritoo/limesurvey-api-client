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

/**
 * Test case of the participants of survey
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class ParticipantsTest extends BaseTestCase
{
    /**
     * An empty collection of participants
     *
     * @var Participants
     */
    private $participantsEmpty;

    /**
     * Participants of 1 survey
     *
     * @var Participants
     */
    private $participantsOfOneSurvey;

    /**
     * Participants of more than 1 survey
     *
     * @var Participants
     */
    private $participantsOfManySurvey;

    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(Participants::class, OopVisibilityType::IS_PUBLIC, 1, 0);
    }

    public function testAdd()
    {
        $this->expectException(DisabledMethodException::class);
        (new Participants())->add('');
    }

    public function testAddMultiple()
    {
        $this->expectException(DisabledMethodException::class);
        (new Participants())->addMultiple([]);
    }

    public function testAddParticipantsWithoutParticipants()
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