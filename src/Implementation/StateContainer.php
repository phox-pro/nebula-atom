<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\Exceptions\StateNotExists;
use Phox\Structures\ObjectCollection;

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
        return $this->children->tryGet($parentClass) ?? new ObjectCollection(State::class);
    }

    /**
     * @param State $state
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
     * @throws StateNotExists
     */
    public function addAfter(State $state, string $parentClass): void
    {
        if (!$this->root->hasObjectClass($parentClass)) {
            throw new StateNotExists($parentClass);
        }

        foreach ($this->children as $children) {
            if (!$children->hasObjectClass($parentClass)) {
                throw new StateNotExists($parentClass);
            }
        }

        $this->children->has($parentClass) ?: $this->children->set($parentClass, new ObjectCollection(State::class));
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