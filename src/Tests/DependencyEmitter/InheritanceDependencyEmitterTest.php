<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class InheritanceDependencyEmitterTest extends \PHPUnit_Framework_TestCase
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
        $this->assertContains('Foo\Bar:6 [extends] on Foo\BarExtends', $deps);
        $this->assertContains('Foo\Bar:6 [extends] on Foo\BarInterface1', $deps);
        $this->assertContains('Foo\Bar:6 [extends] on BarInterface2', $deps);
        $this->assertContains('Foo\Bar:8 [extends] on Foo\SomeTrait', $deps);
    }
}
