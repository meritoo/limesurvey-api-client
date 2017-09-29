<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result\Collection;

use Meritoo\Common\Collection\Collection;
use Meritoo\LimeSurvey\ApiClient\Result\Item\Survey;

/**
 * Collection of surveys (the Survey class instances)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Surveys extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function add($element, $index = null)
    {
        if (null === $index) {
            /* @var Survey $element */
            $index = $element->getId();
        }

        return parent::add($element, $index);
    }

    /**
     * Returns all or active only surveys
     *
     * @param bool $onlyActive (optional) If is set to true, active surveys are returned only. Otherwise - all.
     * @return $this
     */
    public function getAll($onlyActive = false)
    {
        if ($this->isEmpty() || !$onlyActive) {
            return $this;
        }

        $all = new static();

        /* @var Survey $survey */
        foreach ($this as $survey) {
            if ($survey->isActive()) {
                $all->add($survey);
            }
        }

        return $all;
    }
}
