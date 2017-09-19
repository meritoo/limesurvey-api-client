<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Exception;

use Exception;

/**
 * An exception used while create of the session key has failed
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class CreateSessionKeyFailedException extends Exception
{
    /**
     * Class constructor
     *
     * @param string $reason (optional) Reason of failure, e.g. "Invalid user name or password"
     */
    public function __construct($reason = '')
    {
        $message = 'Create of the session key has failed';

        if (!empty($reason)) {
            $message = sprintf('%s. Reason: \'%s\'.', $message, $reason);
        }

        parent::__construct($message);
    }
}
