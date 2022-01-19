<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Nebula\Atom\Implementation\Events\StateRegisteredEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\Interfaces\IAssociativeArray;
use Phox\Structures\Interfaces\ICollection;

/**
 * @property-read StateRegisteredEvent $eStateRegistered
 */
interface IStateContainer
{
    /**
     * @return ICollection<State>
     */
    public function getRoot(): ICollection;

    /**
     * @param string $parentClass
     * @return ICollection<State>
     */
    public function getChildren(string $parentClass): ICollection;

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
     * @return IAssociativeArray<callable>&ICollection<callable>
     */
    public function getFallbacks(): IAssociativeArray&ICollection;
}