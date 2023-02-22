<?php

namespace Phox\Nebula\Atom\Notion;

interface INebulaConfig
{
    public function getProvider(): ?IProvider;
}