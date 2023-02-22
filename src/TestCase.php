<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use PHPUnit\Framework\TestCase as ParentTestCase;

class TestCase extends ParentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ServiceContainerFacade::instance()?->reset();
        Event::clearListeners();
    }
}