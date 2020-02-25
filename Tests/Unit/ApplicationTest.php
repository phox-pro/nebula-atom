<?php

namespace Tests\Unit;

use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\Atom\Implementation\Application;

class ApplicationTest extends TestCase 
{
    /**
     * @test
     */
    public function coreTest()
    {
        $app = get(Application::class);
        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame($app, app());
    }
}