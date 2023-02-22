<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\IServiceContainer;

class StartupConfiguration
{
    public function __construct(
        public readonly bool $registerProvidersFromPackages = true,
        public readonly ?IServiceContainer $container = null,
    ) {
    }
}