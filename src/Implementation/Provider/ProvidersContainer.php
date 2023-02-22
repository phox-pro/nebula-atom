<?php

namespace Phox\Nebula\Atom\Implementation\Provider;

use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\Atom\Notion\IProviderContainer;
use Phox\Structures\Collection;
use Phox\Structures\Interfaces\ICollection;
use Phox\Structures\Interfaces\IEnumerable;

class ProvidersContainer implements IProviderContainer
{
    /**
     * @var Collection<IProvider>
     */
    protected ICollection $providers;

    public function __construct()
    {
        $this->providers = new Collection(type(IProvider::class));
    }

    public function getProviders(): ICollection & IEnumerable
    {
        return $this->providers;
    }

    public function addProvider(IProvider $provider): void
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
        }
    }
}