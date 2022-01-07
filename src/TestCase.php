<?php

namespace Phox\Nebula\Atom;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase 
{
    protected Application $nebula;

    protected function setUp(): void
    {
        $this->nebula = new Application();

        parent::setUp();
    }

    protected function container(): IDependencyInjection
    {
        return $this->nebula->getDIContainer();
    }

    /**
     * Assert that object is singleton
     *
     * @param string|object $object
     * @param string $message
     * @return void
     */
    protected function assertIsSingleton(object|string $object, string $message = ''): void
    {
        $object = is_object($object) ? $object::class : $object;

        $this->assertSame($this->container()->get($object), $this->container()->get($object), $message);
    }
}