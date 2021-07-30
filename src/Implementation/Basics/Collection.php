<?php

namespace Phox\Nebula\Atom\Implementation\Basics;

use ArrayAccess;
use Countable;
use Iterator;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;

/**
 * @template T
 */
class Collection implements Iterator, Countable, ArrayAccess
{
    /**
     * @var class-string<T>|string
     */
    protected string $type;

    /**
     * @var array<mixed, T>
     */
    protected array $list = [];

    /**
     * @param class-string<T>|string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
	 * Get collection type
	 *
	 * @return class-string<T>|string
	 */
	public function getType() : string
	{
		return $this->type;
    }

    /**
	 * Get collection items as array
	 *
	 * @return array<mixed, T>
	 */
	public function all() : array
	{
		return $this->list;
    }

    /**
     * @return T
     */
    public function get(string|int $index): mixed
    {
        return $this[$index] ?? null;
    }

    /**
     * Add item to end of collection
     *
     * @param T $item
     * @return void
     * @throws BadCollectionType
     */
	public function add(mixed $item): void
	{
		$this->check($item);

        array_push($this->list, $item);
    }

    /**
     * Set item by key.
     *
     * @param int|string $key Key for item which not exists in collection
     * @param T $item Item
     * @return void
     * @throws CollectionHasKey|BadCollectionType
     */
	public function set(int|string $key, mixed $item): void
	{
		if (array_key_exists($key, $this->list)) {
		    throw new CollectionHasKey($key);
		}

		$this->replace($key, $item);
    }

    /**
     * Force set item by key
     *
     * @param int|string $key Key
     * @param T $item Item
     * @return void
     * @throws BadCollectionType
     */
	public function replace(int|string $key, mixed $item): void
	{
		$this->check($item);

		$this->list[$key] = $item;
    }

    /**
	 * Get first collection item
	 *
	 * @return T|null
	 */
	public function first(): mixed
	{
		return reset($this->list) ?: null;
    }

    /**
	 * Delete item from collection by key
	 *
	 * @param int|string $index
	 * @return void
	 */
	public function deleteByIndex(int|string $index): void
	{
		if ($this->hasIndex($index)) {
			unset($this->list[$index]);
        }
    }

    /**
     * Delete item from collection by value
     *
     * @param T $item
     * @return void
     */
    public function delete(mixed $item): void
    {
        if (($index = array_search($item, $this->list)) !== false) {
            unset($this->list[$index]);
        }
    }

    /**
     * Collect collection by arguments.
     * NOTE! Array items will be merged!
     *
     * @param T|array<T> ...$items
     * @return void
     * @throws BadCollectionType
     */
    public function collect(...$items): void
    {
        $this->list = [];

        foreach ($items as $item) {
            is_array($item)
                ? $this->merge($item)
                : $this->add($item);
        }
    }

    /**
     * Merge collection with array of items
     *
     * @param array<T> $list
     * @return void
     * @throws BadCollectionType
     */
    public function merge(array $list): void
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
    public function clear(): void
    {
        $this->list = [];
    }

    /**
	 * Check is key exists in collection
	 *
	 * @param int|string $index Key value
	 * @return boolean
	 */
	public function hasIndex(int|string $index) : bool
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
     * @param T $value
     * @return int|string|false
     */
    public function search(mixed $value): int|string|false
    {
        return array_search($value, $this->list);
    }

    /**
     * Check is collection has value
     *
     * @param T $value
     * @return boolean
     */
    public function has(mixed $value) : bool
    {
        return in_array($value, $this->list);
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function current(): mixed
    {
        return current($this->list);
    }

    public function next(): mixed
    {
        return next($this->list);
    }

    public function key(): string|int|null
    {
        return key($this->list);
    }

    public function rewind(): mixed
    {
        return reset($this->list);
    }

    public function valid(): bool
    {
        $key = $this->key();

        return $key !== null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->list[$offset] ?? null;
    }

    /**
     * @throws BadCollectionType
     */
    protected function check(mixed $item): void
	{
        $actualType = is_object($item) ? get_class($item) : gettype($item);

        if ($this->type == 'callable') {
            if (is_callable($item)) {
                return;
            } else {
                throw new BadCollectionType($this, $actualType);
            }
        }

        if (is_object($item)) {
            if ($item instanceof $this->type || $this->type == 'object') {
                return;
            } else {
                throw new BadCollectionType($this, $actualType);
            }
        }

        if ($actualType != $this->type) {
            throw new BadCollectionType($this, $actualType);
        }
	}
}