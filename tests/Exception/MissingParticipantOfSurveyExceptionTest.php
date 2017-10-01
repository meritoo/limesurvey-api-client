<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\Test\ApiClient\Exception;

use Meritoo\Common\Test\Base\BaseTestCase;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\LimeSurvey\ApiClient\Exception\MissingParticipantOfSurveyException;

/**
 * Test case of an exception used when participant of survey is missing
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MissingParticipantOfSurveyExceptionTest extends BaseTestCase
{
    public function testConstructorVisibilityAndArguments()
    {
        static::assertConstructorVisibilityAndArguments(MissingParticipantOfSurveyException::class, OopVisibilityType::IS_PUBLIC, 2, 2);
    }

    /**
     * @param int    $surveyId        ID of survey
     * @param string $email           E-mail address of the participant
     * @param string $expectedMessage Expected exception's message
     *
     * @dataProvider provideSurveyIdAndEmail
     */
    public function testConstructorMessage($surveyId, $email, $expectedMessage)
    {
        $exception = new MissingParticipantOfSurveyException($surveyId, $email);
        static::assertEquals($expectedMessage, $exception->getMessage());
    }

    public function provideSurveyIdAndEmail()
    {
        $template = 'Participant with e-mail %s of survey with ID %s is missing. Maybe was not added to the survey?';

        yield[
            1,
            'lorem@ipsum.com',
            sprintf($template, 'lorem@ipsum.com', 1),
        ];

        yield[
            1234,
            'another@email.comm',
            sprintf($template, 'another@email.comm', 1234),
        ];
    }
}
