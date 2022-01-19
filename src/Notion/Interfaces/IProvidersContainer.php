<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Structures\Interfaces\ICollection;

interface IProvidersContainer
{
    public function addProvider(IProvider $provider): void;

    /**
     * @return ICollection<IProvider>
     */
    public function getProviders(): ICollection;
}