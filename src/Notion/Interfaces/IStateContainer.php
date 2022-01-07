<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\AssociativeObjectCollection;
use Phox\Structures\Collection;
use Phox\Structures\ObjectCollection;

interface IStateContainer
{
    /**
     * @return ObjectCollection<State>
     */
    public function getRoot(): ObjectCollection;

    /**
     * @param string $parentClass
     * @return ObjectCollection<State>
     */
    public function getChildren(string $parentClass): ObjectCollection;

    /**
     * @param State $state
     * @throws StateExistsException
     */
    public function add(State $state): void;

    /**
     * @throws StateExistsException
     */
    public function addAfter(State $state, string $parentClass, ?callable $fallback = null): void;

    public function getState(string $state): ?State;

    /**
     * @return AssociativeObjectCollection<Collection<callable>>
     */
    public function getFallbacks(): AssociativeObjectCollection;
}