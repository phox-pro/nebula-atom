<?php

use Phox\Nebula\Atom\Implementation\AtomProvider;
use Phox\Nebula\Atom\Notion\INebulaConfig;
use Phox\Nebula\Atom\Notion\IProvider;

return new class implements INebulaConfig
{
    public function getProvider(): ?IProvider
    {
        return new AtomProvider();
    }
};