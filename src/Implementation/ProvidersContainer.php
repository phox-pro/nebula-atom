<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IProvider;
use Phox\Nebula\Atom\Notion\Interfaces\IProvidersContainer;
use Phox\Structures\Abstracts\ObjectType;
use Phox\Structures\ObjectCollection;

class ProvidersContainer implements IProvidersContainer
{
    /** @var ObjectCollection<IProvider> */
    protected ObjectCollection $providers;

    public function __construct(protected IDependencyInjection $dependencyInjection)
    {
        $this->providers = new ObjectCollection(ObjectType::fromClass(IProvider::class));
    }

    /**
     * @return ObjectCollection<IProvider>
     */
    public function getProviders(): ObjectCollection
    {
        return $this->providers;
    }

    public function addProvider(IProvider $provider): void
    {
        if ($this->providers->contains($provider)) {
            return;
        }

        $this->providers->add($provider);

        if (is_callable($provider)) {
            $this->dependencyInjection->call($provider);
        }
    }
}