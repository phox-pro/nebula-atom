<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\ObjectCollection;

interface IStateContainer
{
    /**
     * Get root States
     *
     * @return ObjectCollection<State>
     */
    public function getRoot() : ObjectCollection;

    /**
     * Get state children
     *
     * @param string $parentClass
     *
     * @return ObjectCollection<State>
     */
    public function getChildren(string $parentClass) : ObjectCollection;

    /**
     * Add State to container
     */
    public function add(State $state): void;

    /**
     * Add State to container and set as child
     *
     * @param State $state
     * @param class-string<State> $parentClass
     *
     * @return void
     */
    public function addAfter(State $state, string $parentClass): void;

    /**
     * @param class-string<State> $state
     * @return State|null
     */
    public function getState(string $state): ?State;
}