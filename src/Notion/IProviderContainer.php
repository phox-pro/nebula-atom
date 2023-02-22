<?php

namespace Phox\Nebula\Atom\Notion;

use Phox\Structures\Interfaces\ICollection;
use Phox\Structures\Interfaces\IEnumerable;

interface IProviderContainer
{
    /**
     * @return ICollection<IProvider>&IEnumerable<IProvider>
     */
    public function getProviders(): ICollection & IEnumerable;

    public function addProvider(IProvider $provider): void;
}