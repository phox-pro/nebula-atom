<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Application;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;

class TestCase extends FrameworkTestCase
{
    protected function setUp(): void
    {
        \Phox\Nebula\Atom\Files\init();

        get(Application::class)->addProvider(make(AtomProvider::class));
        get(IStateContainer::class)->clearListeners();
        
        parent::setUp();
    }

    /**
     * Assert that object is singleton
     *
     * @param string|object $object
     * @param string $message
     * @return void
     */
    protected function assertIsSingleton($object, string $message = '')
    {
        $object = is_object($object) ? get_class($object) : $object;
        $this->assertSame(get($object), get($object), $message);
    }
}
