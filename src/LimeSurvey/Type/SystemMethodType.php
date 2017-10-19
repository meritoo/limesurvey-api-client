<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Type;

use Meritoo\Common\Type\Base\BaseType;

/**
 * Type of system-related method used while talking with LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class SystemMethodType extends BaseType
{
    const className = 'Meritoo\LimeSurvey\ApiClient\Type\SystemMethodType';

    /**
     * Create and return a session key
     *
     * @var string
     */
    const GET_SESSION_KEY = 'get_session_key';

    /**
     * Close the RPC session
     *
     * @var string
     */
    const RELEASE_SESSION_KEY = 'release_session_key';
}
