<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Structures\Abstracts\Type;
use Phox\Structures\Collection;

abstract class Event implements IEvent
{
    /** @var Collection<callable> */
    protected Collection $listeners;

    public function __construct()
    {
        $this->listeners = new Collection(Type::CALLABLE);
    }

    public function listen(callable $listener): void
    {
        if (!$this->listeners->contains($listener)) {
            $this->listeners->add($listener);
        }
    }

    public function getListeners() : Collection
    {
        return $this->listeners;
    }
}