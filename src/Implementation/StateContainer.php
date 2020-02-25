<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\MustExtends;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Atom\Implementation\Exceptions\StateNotExists;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Implementation\Exceptions\MustImplementInterface;

class StateContainer implements IStateContainer 
{
    protected Collection $root;

    protected Collection $children;

    protected Collection $all;

    public function __construct()
    {
        $this->root = new Collection('string');
        $this->children = new Collection(Collection::class);
        $this->all = new Collection('string');
    }

    public function getAll(): Collection
    {
        return $this->all;
    }

    public function getRoot(): Collection
    {
        return $this->root;
    }

    public function getChildren(string $parentClass): Collection
    {
        return ($parentIndex = $this->all->search($parentClass)) === false
            ? new Collection('string')
            : ($this->children->hasIndex($parentIndex)
                ? $this->children[$parentIndex]
                : new Collection('string'));
    }

    public function add(string $stateClass)
    {
        $this->addToAll($stateClass);
        $this->root->add($stateClass);
    }

    public function addAfter(string $stateClass, string $parentClass)
    {
        $parentIndex = $this->all->search($parentClass);
        if ($parentIndex === false) {
            error(StateNotExists::class, $parentClass);
        }
        $this->addToAll($stateClass);
        $this->children->hasIndex($parentIndex) ?: $this->children->set($parentIndex, new Collection('string'));
        $this->children->get($parentIndex)->add($stateClass);
    }

    protected function addToAll(string $stateClass)
    {
        if (!is_subclass_of($stateClass, State::class)) {
            error(MustExtends::class, $stateClass, State::class);
        }
        if ($this->all->has($stateClass)) {
            error(StateExistsException::class, $stateClass);
        }
        $this->all->add($stateClass);
    }
}