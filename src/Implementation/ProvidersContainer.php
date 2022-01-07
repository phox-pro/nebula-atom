<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Structures\Abstracts\ObjectType;
use Phox\Structures\ObjectCollection;

class ProvidersContainer
{
    /** @var ObjectCollection<Provider> */
    protected ObjectCollection $providers;

    public function __construct(protected IDependencyInjection $dependencyInjection)
    {
        $this->providers = new ObjectCollection(ObjectType::fromClass(Provider::class));
    }

    /**
     * @return ObjectCollection<Provider>
     */
    public function getProviders(): ObjectCollection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): void
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