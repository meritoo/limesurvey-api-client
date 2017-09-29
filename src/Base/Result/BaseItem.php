<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Base\Result;

/**
 * Base class for one item of result/data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseItem
{
    /**
     * Class constructor
     *
     * @param array $data (optional) Data to set in properties of the item
     */
    public function __construct(array $data = [])
    {
        $this->setValues($data);
    }

    /**
     * Sets given value in given property
     *
     * @param string $property Name of property to set the value
     * @param mixed  $value    Value to set in property
     * @return mixed
     */
    abstract public function setValue($property, $value);

    /**
     * Sets values in each property of the item
     *
     * @param array $data Data to set in properties of the item
     * @return $this
     */
    private function setValues(array $data)
    {
        /*
         * Oops, no data
         */
        if (empty($data)) {
            return $this;
        }

        /*
         * Let's iterate through each property
         */
        foreach ($data as $property => $value) {
            $this->setValue($property, $value);
        }

        return $this;
    }
}
