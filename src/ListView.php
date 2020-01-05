<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable;

class ListView implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    public $vars = array(
        'data' => array(),
        'pagination' => array(
            'count' => 0,
            'total' => 0,
            'pages' => 0,
            'page' => 0,
            'limit' => 0,
        ),
        'config' => array(),
        'state' => array(),
    );

    /**
     * Returns a data offset (implements \ArrayAccess).
     *
     * @param string|int $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->vars['data'][$offset];
    }

    /**
     * Returns whether the given data offset exists (implements \ArrayAccess).
     *
     * @param string|int $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->vars['data'][$offset]);
    }

    /**
     * Set given data offset (implements \ArrayAccess).
     *
     * @param string|int $offset
     */
    public function offsetSet($offset, $value)
    {
        $this->vars['data'][$offset] = $value;
    }

    /**
     * Removes given data offset (implements \ArrayAccess).
     *
     * @param string|int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->vars['data'][$offset]);
    }

    /**
     * Returns an iterator to iterate over loaded objects (implements \IteratorAggregate).
     *
     * @return \ArrayIterator|ListView[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->vars['data']);
    }

    /**
     * Implements \Countable.
     *
     * @return int The number of children views
     */
    public function count(): int
    {
        return \count($this->vars['data']);
    }

    /**
     * Implements \JsonSerializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->vars;
    }
}
