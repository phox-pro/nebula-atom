<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Event;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Throwable;
use Phox\Nebula\Atom\Implementation\Basics\Collection;

class ExceptionHandler extends Event
{
    protected Collection $listeners;
    protected IDependencyInjection $dependencyInjection;

    public function __construct()
    {
        parent::__construct();

        $this->listeners = new Collection(Collection::class);
        $this->dependencyInjection = Functions::container()->get(IDependencyInjection::class);
    }

    public function execute(Throwable $throwable)
    {
        $exceptionListeners = $this->listeners->get($throwable::class);

        if (is_null($exceptionListeners)) {
            return;
        }

        foreach ($exceptionListeners as $exceptionListener) {
            $this->dependencyInjection->call($exceptionListener, [$throwable]);
        }
    }

    /**
     * @throws Exceptions\CollectionHasKey
     * @throws Exceptions\BadCollectionType
     */
    public function listen(callable $listener, string $exceptionClass = Throwable::class): void
    {
        $this->listeners->hasIndex($exceptionClass) ?: $this->listeners->set($exceptionClass, new Collection('callable'));

        /** @var Collection<callable> $exceptionListeners */
        $exceptionListeners = $this->listeners->get($exceptionClass);
        $exceptionListeners->has($listener) ?: $exceptionListeners->add($listener);
    }
}