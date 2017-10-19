<?php

namespace Meritoo\Common\Iterator;

use Iterator;

/**
 * Iterator of iterable values
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class CommonIterator implements Iterator
{
    /**
     * Index/Position of current value
     *
     * @var int
     */
    private $currentIndex = 0;

    /**
     * Maximum index/position of current value
     *
     * @var int
     */
    private $maxIndex = 0;

    /**
     * Values to iterate
     *
     * @var array
     */
    private $values = [];

    /**
     * Class constructor
     *
     * @param array $values Values to iterate
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->values[$this->currentIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (0 <= $this->currentIndex && $this->maxIndex > $this->currentIndex) {
            ++$this->currentIndex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->currentIndex = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return 0 <= $this->currentIndex && $this->maxIndex >= $this->currentIndex;
    }
}
