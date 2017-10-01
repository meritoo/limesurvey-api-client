<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\LimeSurvey\ApiClient\Result;

use Meritoo\Common\Collection\Collection;
use Meritoo\LimeSurvey\ApiClient\Base\Result\BaseItem;
use Meritoo\LimeSurvey\ApiClient\Exception\CannotProcessDataException;
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
     * Status, information returned instead of usual/normal result
     *
     * @var string
     */
    private $status;

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
        $this->setRawDataAndStatus($rawData);
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
     * @throws CannotProcessDataException
     */
    public function getData($raw = false)
    {
        /*
         * Raw data should be returned only?
         * Let's do it
         */
        if ($raw) {
            return $this->rawData;
        }

        /*
         * Status is unknown?
         * Let's process the raw data
         */
        if (empty($this->status)) {
            return $this->getProcessedData($this->rawData);
        }

        /*
         * Oops, the raw data returned by the LimeSurvey's API cannot be processed, because status was provided.
         * Well, probably something is broken and... there is no data.
         */
        throw new CannotProcessDataException($this->status);
    }

    /**
     * Returns status, information returned instead of usual/normal result
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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

        /*
         * Result is unknown and it should be iterable the result is an array?
         * Let's prepare and return collection
         */
        if ((null === $processed && MethodType::isResultIterable($this->method)) || is_array($processed)) {
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

    /**
     * Sets status, information returned instead of usual/normal result and raw data returned by the LimeSurvey's API
     *
     * @param array $rawData Raw data returned by the LimeSurvey's API
     */
    private function setRawDataAndStatus(array $rawData)
    {
        /*
         * Status was provided?
         * Well, probably something is broken and... there is no data
         */
        if (isset($rawData['status'])) {
            $this->status = trim($rawData['status']);
            $rawData = [];
        }

        $this->rawData = $rawData;
    }
}
