<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Events\StateRegisteredEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Structures\Collection;
use Phox\Structures\ObjectCollection;

class StateContainer
{
    public StateRegisteredEvent $eStateRegistered;

    /** @var ObjectCollection<State> */
    protected ObjectCollection $root;

    /** @var ObjectCollection<ObjectCollection<State>> */
    protected ObjectCollection $children;

    /** @var Collection<Collection<callable>> */
    protected Collection $fallbacks;

    public function __construct()
    {
        $this->root = new ObjectCollection(State::class);
        $this->children = new ObjectCollection(ObjectCollection::class);
        $this->fallbacks = new Collection(Collection::class);

        $this->eStateRegistered = new StateRegisteredEvent();

        $this->eStateRegistered->listen(
            fn(State $state) => $this->fallbacks->has($state::class)
                ? $this->fallbacks->remove($state::class)
                : null
        );

        Functions::nebula()->eCompleted->listen(function () {
            foreach ($this->fallbacks as $fallbacks) {
                foreach ($fallbacks as $stateClass => $fallback) {
                    Functions::container()->call($fallback, [$stateClass]);
                }
            }
        });
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

        $this->eStateRegistered->notify($state);
    }

    /**
     * @throws StateExistsException
     */
    public function addAfter(State $state, string $parentClass, ?callable $fallback = null): void
    {
        if (!$this->root->hasObjectClass($parentClass)) {
            $found = false;

            foreach ($this->children as $child) {
                if ($child->hasObjectClass($parentClass)) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $this->eStateRegistered->listen(function (State $registeredState) use ($parentClass, $state) {
                    if ($registeredState instanceof $parentClass) {
                        $this->addAfter($state, $parentClass);
                    }
                });

                if (!is_null($fallback)) {
                    $this->fallbacks->has($parentClass) ?: $this->fallbacks->set($parentClass, new Collection('callable'));
                    $this->fallbacks->get($parentClass)->add($fallback);
                }

                return;
            }
        }

        if (!is_null($this->getState($state::class))) {
            throw new StateExistsException($state::class);
        }

        $this->children->has($parentClass) ?: $this->children->set($parentClass, new ObjectCollection(State::class));
        $this->children->get($parentClass)->add($state);

        $this->eStateRegistered->notify($state);
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