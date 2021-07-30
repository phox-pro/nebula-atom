<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Basics\ObjectCollection;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\Exceptions\StateNotExists;

class StateContainer implements IStateContainer 
{
    /** @var ObjectCollection<State> */
    protected ObjectCollection $root;

    /** @var ObjectCollection<ObjectCollection<State>> */
    protected ObjectCollection $children;

    public function __construct()
    {
        $this->root = new ObjectCollection(State::class);
        $this->children = new ObjectCollection(ObjectCollection::class);
    }

    public function getRoot(): ObjectCollection
    {
        return $this->root;
    }

    public function getChildren(string $parentClass): ObjectCollection
    {
        return $this->children->get($parentClass) ?? new ObjectCollection(State::class);
    }

    /**
     * @param State $state
     * @throws Exceptions\BadCollectionType
     * @throws StateExistsException
     */
    public function add(State $state): void
    {
        if (!is_null($this->getState($state::class))) {
            throw new StateExistsException($state::class);
        }

        $this->root->add($state);
    }

    /**
     * @throws Exceptions\CollectionHasKey
     * @throws Exceptions\BadCollectionType
     * @throws StateNotExists
     */
    public function addAfter(State $state, string $parentClass): void
    {
        if (!$this->root->hasClass($parentClass)) {
            throw new StateNotExists($parentClass);
        }

        foreach ($this->children as $children) {
            if (!$children->hasClass($parentClass)) {
                throw new StateNotExists($parentClass);
            }
        }

        $this->children->hasIndex($parentClass) ?: $this->children->set($parentClass, new ObjectCollection(State::class));
        $this->children->get($parentClass)->add($state);
    }

    public function getState(string $state): ?State
    {
        foreach ($this->root as $existsState) {
            if ($state == $existsState::class) {
                return $existsState;
            }
        }

        foreach ($this->children as $children) {
            foreach ($children as $child) {
                if ($state == $child::class) {
                    return $child;
                }
            }
        }

        return null;
    }
}