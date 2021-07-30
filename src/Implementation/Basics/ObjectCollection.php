<?php

namespace Phox\Nebula\Atom\Implementation\Basics;

use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use stdClass;

/**
 * @template T
 * @method class-string<T> getType()
 */
class ObjectCollection extends Collection
{
    /**
     * @var class-string<T>
     */
    protected string $type;

    /**
     * @param class-string<T> $type
     * @throws BadCollectionType
     */
    public function __construct(string $type)
    {
        if (!class_exists($type)) {
            throw new BadCollectionType($this, $type);
        }

        parent::__construct($type);
    }

    /**
     * @param class-string<T> $class
     *
     * @return bool
     */
    public function hasClass(string $class): bool
    {
        foreach ($this->list as $item) {
            if (get_class($item) == $class) {
                return true;
            }
        }

        return false;
    }

    protected function check($item): void
    {
        $itemType = is_object($item)
            ? get_class($item)
            : gettype($item);

        if (!($item instanceof $this->type)) {
            throw new BadCollectionType($this, $itemType);
        }
    }
}