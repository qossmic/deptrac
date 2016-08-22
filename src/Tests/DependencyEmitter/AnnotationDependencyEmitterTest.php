<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\DependencyEmitter\AnnotationDependencyEmitter;

class AnnotationDependencyEmitterTest extends \PHPUnit_Framework_TestCase
{
    use EmitterTrait;

    public function testGetName()
    {
        $this->assertEquals('AnnotationDependencyEmitter', (new AnnotationDependencyEmitter())->getName());
    }

    public function testSupportsParser()
    {
        $this->assertTrue((new AnnotationDependencyEmitter())->supportsParser($this->prophesize(NikicPhpParser::class)->reveal()));
    }

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new AnnotationDependencyEmitter(),
            [new \SplFileInfo(__DIR__.'/Fixtures/Bar.php'), new \SplFileInfo(__DIR__.'/Fixtures/Baz.php')]
        );

        $this->assertCount(6, $deps);
        $this->assertContains('Foo\Foo:12 on Foo\Foo', $deps);
        $this->assertContains('Foo\Foo:17 on Foo\Foo', $deps);
        $this->assertContains('Foo\Foo:22 on Baz\Bar', $deps);
        $this->assertContains('Foo\Foo:30 on Foo\Foo', $deps);
        $this->assertContains('Foo\Foo:30 on Foo\Foo', $deps);
        $this->assertContains('Foo\Foo:30 on Baz\Bar', $deps);
    }
}
