<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\States\DefineState;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase 
{
    protected function setUp(): void
    {
        init();
        get(IStateContainer::class)->clearListeners();
        parent::setUp();
    }
}