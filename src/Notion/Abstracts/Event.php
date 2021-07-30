<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;

abstract class Event implements IEvent
{
    /** @var Collection<callable> */
    protected Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection('callable');
    }

    /**
     * @throws BadCollectionType
     */
    public function listen(callable $listener): void
    {
        if (!$this->listeners->has($listener)) {
            $this->listeners->add($listener);
        }
    }

    public function notify(...$params): void
    {
        $this->notifyRaw($params);
    }

    public function notifyRaw(array $params = []): void
    {
        Functions::container()->singleton($this, IEvent::class);

        foreach ($this->listeners as $listener) {
            Functions::container()->call($listener, $params);
        }

        Functions::container()->deleteDependency(IEvent::class);
    }

    public function getListeners() : Collection
    {
        return $this->listeners;
    }
}