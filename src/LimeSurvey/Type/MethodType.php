<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Type;

use Meritoo\Common\Type\Base\BaseType;
use Meritoo\LimeSurvey\ApiClient\Exception\UnknownMethodException;

/**
 * Type of method used while talking with LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class MethodType extends BaseType
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Type\MethodType';

    /**
     * Add participants to the tokens collection of the survey
     *
     * Returns the inserted data including additional new information like the Token entry ID and the token string.
     * In case of errors in some data, return it in errors.
     *
     * @var string
     */
    const ADD_PARTICIPANTS = 'add_participants';

    /**
     * Add a response to the survey responses collection.
     * Returns the id of the inserted survey response.
     *
     * @var string
     */
    const ADD_RESPONSE = 'add_response';

    /**
     * Export statistics of a survey to a user
     *
     * @var string
     */
    const EXPORT_STATISTICS = 'export_statistics';

    /**
     * Get settings of a token/participant of a survey
     *
     * @var string
     */
    const GET_PARTICIPANT_PROPERTIES = 'get_participant_properties';

    /**
     * Get properties of a question in a survey
     *
     * @var string
     */
    const GET_QUESTION_PROPERTIES = 'get_question_properties';

    /**
     * Get survey summary, regarding token usage and survey participation
     *
     * @var string
     */
    const GET_SUMMARY = 'get_summary';

    /**
     * Return the IDs and properties of token/participants of a survey
     *
     * @var string
     */
    const LIST_PARTICIPANTS = 'list_participants';

    /**
     * Return the ids and info of (sub-)questions of a survey/group
     *
     * @var string
     */
    const LIST_QUESTIONS = 'list_questions';

    /**
     * List the surveys belonging to a user
     *
     * @var string
     */
    const LIST_SURVEYS = 'list_surveys';

    /**
     * Get list the ids and info of users
     *
     * @var string
     */
    const LIST_USERS = 'list_users';

    /**
     * Returns validated name of method to call or throws an exception (if method is incorrect)
     *
     * @param string $method Name of method to call. One of this class constants.
     * @return string
     *
     * @throws UnknownMethodException
     */
    public static function getValidatedMethod($method)
    {
        if ((new static())->isCorrectType($method) || (new SystemMethodType())->isCorrectType($method)) {
            return $method;
        }

        throw new UnknownMethodException($method);
    }

    /**
     * Returns information if result provided by the API is iterable
     *
     * @param string $method Name of called method while talking to the LimeSurvey's API. One of this class constants.
     * @return bool
     */
    public static function isResultIterable($method)
    {
        $method = static::getValidatedMethod($method);

        return in_array($method, [
            static::ADD_PARTICIPANTS,
            static::LIST_PARTICIPANTS,
            static::LIST_QUESTIONS,
            static::LIST_SURVEYS,
            static::LIST_USERS,
        ]);
    }
}
