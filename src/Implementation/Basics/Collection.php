<?php

namespace Phox\Nebula\Atom\Implementation\Basics;

use ArrayAccess;
use Countable;
use Iterator;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;

class Collection implements Iterator, Countable, ArrayAccess
{
    /**
     * Collection type
     */
    protected string $type;

    /**
     * List of collection items
     */
    protected array $list = [];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
	 * Get collection type
	 *
	 * @return string
	 */
	public function getType() : string
	{
		return $this->type;
    }

    /**
	 * Get collection items as array
	 *
	 * @return array
	 */
	public function all() : array
	{
		return $this->list;
    }

    /**
     * Get value by index
     *
     * @param string|int $index
     * @return void
     */
    public function get($index)
    {
        return $this[$index];
    }

    /**
	 * Add item to end of collection
	 *
	 * @param mixed $item
	 * @return mixed
	 */
	public function add($item)
	{
		$this->check($item);
        array_push($this->list, $item);
        return array_key_last($this->list);
    }
    
    /**
	 * Set item by key.
	 *
	 * @param mixed $key Key for item which not exists in collection
	 * @param mixed $item Item
	 * @return void
	 */
	public function set($key, $item)
	{
		if (array_key_exists($key, $this->list)) {
            error(CollectionHasKey::class, $key);
		}
		$this->replace($key, $item);
    }
    
    /**
	 * Force set item by key
	 *
	 * @param mixed $key Key
	 * @param mixed $item Item
	 * @return void
	 */
	public function replace($key, $item)
	{
		$this->check($item);
		$this->list[$key] = $item;
    }

    /**
	 * Get first collection item
	 *
	 * @return mixed
	 */
	public function first()
	{
		return reset($this->list) ?: null;
    }

    /**
	 * Delete item from collection by key
	 *
	 * @param mixed $index
	 * @return void
	 */
	public function deleteByIndex($index)
	{
		if ($this->hasIndex($index)) {
			unset($this->list[$index]);
        }
    }

    /**
     * Delete item from collection by value
     *
     * @param mixed $item
     * @return void
     */
    public function delete($item)
    {
        if (($index = array_search($item, $this->list)) !== false) {
            unset($this->list[$index]);
        }
    }

    /**
     * Collect collection by arguments.
     * NOTE! Array items will be merged!
     *
     * @param mixed ...$items
     * @return void
     */
    public function collect(...$items)
    {
        $this->list = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $this->merge($item);
            } else {
                $this->add($item);
            }
        }
    }

    /**
     * Merge collection with array of items
     *
     * @param array $list
     * @return void
     */
    public function merge(array $list)
    {
        foreach ($list as $value) {
            $this->check($value);
        }
        $this->list = array_merge($this->list, $list);
    }

    /**
     * Check collection is empty
     *
     * @return boolean
     */
	public function empty() : bool
	{
		return empty($this->list);
    }

    /**
     * Clear collection
     *
     * @return void
     */
    public function clear()
    {
        $this->list = [];
    }

    /**
	 * Check is key exists in collection
	 *
	 * @param mixed $index Key value
	 * @return boolean
	 */
	public function hasIndex($index) : bool
	{
		return array_key_exists($index, $this->list);
    }
    
    /**
     * Get all keys in collection
     *
     * @return array
     */
    public function keys() : array
    {
        return array_keys($this->list);
    }

    /**
     * Search value index
     *
     * @param mixed $value
     * @return mixed
     */
    public function search($value)
    {
        return array_search($value, $this->list);
    }

    /**
     * Check is collection has value
     *
     * @param mixed $value
     * @return boolean
     */
    public function has($value) : bool
    {
        return in_array($value, $this->list);
    }

    public function count()
    {
        return count($this->list);
    }

    public function current()
    {
        return current($this->list);
    }

    public function next()
    {
        return next($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function rewind()
    {
        return reset($this->list);
    }

    public function valid()
    {
        $key = $this->key();
        return $key !== null && $key !== false;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->list[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    private function check($item)
	{
		if ($this->type == 'callable') {
            is_callable($item) ?: error(BadCollectionType::class, $this, is_object($item) ? get_class($item) : gettype($item));
		} elseif (is_object($item)) {
			is_a($item, $this->type) ?: error(BadCollectionType::class, $this, get_class($item));
		} else {
			(($actualType = gettype($item)) == $this->type) ?: error(BadCollectionType::class, $this, $actualType);
        }
	}
}