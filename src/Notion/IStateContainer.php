<?php

namespace Phox\Nebula\Atom\Notion;

use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Structures\Interfaces\ICollection;
use Phox\Structures\Interfaces\IEnumerableArray;

interface IStateContainer
{
    /**
     * @return ICollection<State>&IEnumerableArray<State>
     */
    public function getRoot(): IEnumerableArray & ICollection;

    /**
     * @param class-string<State> $parentClass
     * @return ICollection<State>&IEnumerableArray<State>
     */
    public function getChildren(string $parentClass): IEnumerableArray & ICollection;

    /**
     * @param State $state
     * @return void
     */
    public function add(State $state): void;

    /**
     * @param State $state
     * @param class-string<State> $parentStateClass
     * @param callable(class-string<State>): void|null $fallback
     * @return void
     */
    public function addAfter(State $state, string $parentStateClass, ?callable $fallback = null): void;

    /**
     * @param class-string<State> $stateClass
     * @return State|null
     */
    public function getState(string $stateClass): ?State;
}