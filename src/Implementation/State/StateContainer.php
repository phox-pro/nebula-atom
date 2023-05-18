<?php

namespace Phox\Nebula\Atom\Implementation\State;

use Phox\Nebula\Atom\Implementation\Events\ApplicationCompletedEvent;
use Phox\Nebula\Atom\Implementation\Events\StateRegisteredEvent;
use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\IStateContainer;
use Phox\Structures\Abstracts\Type;
use Phox\Structures\AssociativeCollection;
use Phox\Structures\Collection;
use Phox\Structures\Interfaces\IAssociativeArray;
use Phox\Structures\Interfaces\ICollection;
use Phox\Structures\Interfaces\IEnumerableArray;

class StateContainer implements IStateContainer
{
    /**
     * @var Collection<State>
     */
    protected IEnumerableArray & ICollection $root;

    /**
     * @var AssociativeCollection<IEnumerableArray<State>&ICollection<State>>
     */
    protected IAssociativeArray $children;

    /**
     * @var AssociativeCollection<callable>
     */
    protected IAssociativeArray $fallbacks;

    public function __construct()
    {
        $this->root = new Collection(type(State::class));
        $this->children = new AssociativeCollection(type(IEnumerableArray::class));
        $this->fallbacks = new AssociativeCollection(Type::Callable);

        $this->setFallbacksCleaner();
        $this->setFallbacksCaller();
    }

    public function getRoot(): IEnumerableArray & ICollection
    {
        return $this->root;
    }

    public function getChildren(string $parentClass): IEnumerableArray & ICollection
    {
        return $this->children->tryGet($parentClass, new AssociativeCollection(type(State::class)));
    }

    /**
     * @throws StateExistsException
     */
    public function add(State $state): void
    {
        if (!is_null($this->getState($state::class))) {
            throw new StateExistsException($state::class);
        }

        $this->root->add($state);

        (new StateRegisteredEvent($state))->notify();
    }

    /**
     * @throws StateExistsException
     */
    public function addAfter(State $state, string $parentStateClass, ?callable $fallback = null): void
    {
        if (!is_null($this->getState($state::class))) {
            throw new StateExistsException($state::class);
        }

        if (!is_null($fallback)) {
            $this->fallbacks->set($state::class, $fallback);
        }

        is_null($this->getState($parentStateClass))
            ? $this->addListenerForParentState($state, $parentStateClass)
            : $this->addToChildren($state, $parentStateClass);
    }

    public function getState(string $stateClass): ?State
    {
        foreach ($this->root as $item) {
            if ($stateClass === $item::class) {
                return $item;
            }
        }

        foreach ($this->children as $children) {
            foreach ($children as $child) {
                if ($stateClass === $child::class) {
                    return $child;
                }
            }
        }

        return null;
    }

    protected function addToChildren(State $state, string $parentStateClass): void
    {
        $this->children->has($parentStateClass)
            ?: $this->children->set($parentStateClass, new Collection(type(State::class)));

        $this->children->get($parentStateClass)->add($state);

        (new StateRegisteredEvent($state))->notify();
    }

    protected function setFallbacksCleaner(): void
    {
        StateRegisteredEvent::listen(function (StateRegisteredEvent $event) {
            if ($this->fallbacks->has($event->state::class)) {
                $this->fallbacks->remove($event->state::class);
            }
        });
    }

    protected function setFallbacksCaller(): void
    {
        ApplicationCompletedEvent::listen(function (ApplicationCompletedEvent $event) {
            foreach ($this->fallbacks as $stateClass => $callback) {
                call_user_func($callback, $stateClass);
            }
        });
    }

    protected function addListenerForParentState(State $state, string $parentStateClass): void
    {
        StateRegisteredEvent::listen(function (StateRegisteredEvent $event) use ($parentStateClass, $state) {
            if ($event->state::class === $parentStateClass) {
                $this->addToChildren($state, $parentStateClass);
            }
        });
    }
}