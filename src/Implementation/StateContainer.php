<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Exceptions\StateExistsException;
use Phox\Nebula\Atom\Notion\Abstracts\State;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEventManager;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Structures\Abstracts\Type;
use Phox\Structures\AssociativeObjectCollection;
use Phox\Structures\Collection;
use Phox\Structures\ObjectCollection;

class StateContainer implements IStateContainer
{
    public StateRegisteredEvent $eStateRegistered;

    /** @var ObjectCollection<State> */
    protected ObjectCollection $root;

    /** @var AssociativeObjectCollection<ObjectCollection> */
    protected AssociativeObjectCollection $children;

    /** @var AssociativeObjectCollection<Collection<callable>> */
    protected AssociativeObjectCollection $fallbacks;

    public function __construct(protected IEventManager $eventManager)
    {
        $this->root = new ObjectCollection(type(State::class));
        $this->children = new AssociativeObjectCollection(type(ObjectCollection::class));
        $this->fallbacks = new AssociativeObjectCollection(type(Collection::class));

        $this->eStateRegistered = new StateRegisteredEvent();

        $this->eStateRegistered->listen(
            function (IEvent $state): void {
                if ($this->fallbacks->has($state::class)) {
                    $this->fallbacks->remove($state::class);
                }
            }
        );
    }

    public function getRoot(): ObjectCollection
    {
        return $this->root;
    }

    public function getChildren(string $parentClass): ObjectCollection
    {
        return $this->children->tryGet($parentClass) ?? new ObjectCollection(type(State::class));
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

        $this->eStateRegistered->setState($state);

        $this->eventManager->notify($this->eStateRegistered);

        $this->eStateRegistered->setState(null);
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
                $this->eStateRegistered->listen(function (StateRegisteredEvent $event) use ($parentClass, $state) {
                    $registeredState = $event->getState();

                    if ($registeredState instanceof $parentClass) {
                        $this->addAfter($state, $parentClass);
                    }
                });

                if (!is_null($fallback)) {
                    $this->fallbacks->has($parentClass) ?: $this->fallbacks->set($parentClass, new Collection(Type::CALLABLE));
                    $this->fallbacks->get($parentClass)->add($fallback);
                }

                return;
            }
        }

        if (!is_null($this->getState($state::class))) {
            throw new StateExistsException($state::class);
        }

        $this->children->has($parentClass) ?: $this->children->set($parentClass, new ObjectCollection(type(State::class)));
        $this->children->get($parentClass)->add($state);

        $this->eventManager->notify($state);
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

    public function getFallbacks(): AssociativeObjectCollection
    {
        return $this->fallbacks;
    }
}