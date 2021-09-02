<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Structures\ListedObjectCollection;

class ProvidersContainer
{
    protected ListedObjectCollection $providers;

    public function __construct()
    {
        $this->providers = new ListedObjectCollection(Provider::class);
    }

    public function getProviders(): ListedObjectCollection
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
            Functions::container()->call($provider);
        }
    }
}