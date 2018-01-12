<?php

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;

class InheritanceDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName()
    {
        $this->assertEquals('InheritanceDependencyEmitter', (new InheritanceDependencyEmitter())->getName());
    }

    public function testSupportsParser()
    {
        $this->assertTrue((new InheritanceDependencyEmitter())->supportsParser($this->prophesize(NikicPhpParser::class)->reveal()));
    }

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new InheritanceDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        $this->assertCount(4, $deps);
        $this->assertContains('Foo\Bar:6 on Foo\BarExtends', $deps);
        $this->assertContains('Foo\Bar:6 on Foo\BarInterface1', $deps);
        $this->assertContains('Foo\Bar:6 on BarInterface2', $deps);
        $this->assertContains('Foo\Bar:8 on Foo\SomeTrait', $deps);
    }
}
