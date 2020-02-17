<?php

namespace Phox\Nebula\Atom;

use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase 
{
    protected function setUp(): void
    {
        init();
        container()->reset();
        parent::setUp();
    }
}