<?php

use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;

class ServiceContainerTest extends TestCase
{
    /**
     * @test
     */
    public function initDI()
    {
        $this->assertTrue(function_exists('\Phox\Nebula\Atom\init'));
        $this->assertTrue(function_exists('container'));
        $result = container();
        $this->assertInstanceOf(IDependencyInjection::class, $result);
    }
}