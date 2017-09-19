<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result;

use Meritoo\Common\Collection\Collection;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Result\Processor\ResultProcessor;
use Meritoo\LimeSurvey\ApiClient\Type\MethodType;

/**
 * Result with data fetched while talking to the LimeSurvey's API
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Result
{
    /**
     * Name of called method while talking to the LimeSurvey's API. One of the MethodType class constants.
     *
     * @var string
     */
    private $method;

    /**
     * Raw data returned by the LimeSurvey's API
     *
     * @var array
     */
    private $rawData;

    /**
     * Processor of the raw data fetched while talking to the LimeSurvey's API
     *
     * @var ResultProcessor
     */
    private $resultProcessor;

    /**
     * Class constructor
     *
     * @param string $method  Name of called method while talking to the LimeSurvey's API. One of the MethodType
     *                        class constants.
     * @param array  $rawData Raw data returned by the LimeSurvey's API
     */
    public function __construct($method, array $rawData)
    {
        $this->method = MethodType::getValidatedMethod($method);
        $this->rawData = $rawData;
    }

    /**
     * Returns information if the result contains any data
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->rawData);
    }

    /**
     * Returns data returned by the LimeSurvey's API
     *
     * @param bool $raw (optional) If is set to true, raw data provided by the LimeSurvey's API will be returned.
     *                  Otherwise - prepared/processed.
     * @return array|Collection|BaseItem
     */
    public function getData($raw = false)
    {
        if ($raw) {
            return $this->rawData;
        }

        return $this->getProcessedData($this->rawData);
    }

    /**
     * Returns processed data based on the raw data returned by the LimeSurvey's API
     *
     * @param array $rawData Raw data returned by the LimeSurvey's API
     * @return Collection|BaseItem
     */
    private function getProcessedData(array $rawData)
    {
        $processed = $this
            ->getResultProcessor()
            ->process($this->method, $rawData);

        if (null === $processed || is_array($processed)) {
            $collection = new Collection();

            if (is_array($processed)) {
                $collection->addMultiple($processed);
            }

            return $collection;
        }

        return $processed;
    }

    /**
     * Returns processor of the raw data fetched while talking to the LimeSurvey's API
     *
     * @return ResultProcessor
     */
    private function getResultProcessor()
    {
        if (null === $this->resultProcessor) {
            $this->resultProcessor = new ResultProcessor();
        }

        return $this->resultProcessor;
    }
}
