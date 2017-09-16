<?php

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\DependencyEmitter\BasicDependencyEmitter;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class BasicDependencyEmitterTest extends \PHPUnit_Framework_TestCase
{
    use EmitterTrait;

    public function testGetName()
    {
        $this->assertEquals('BasicDependencyEmitter', (new BasicDependencyEmitter())->getName());
    }

    public function testSupportsParser()
    {
        $this->assertTrue((new BasicDependencyEmitter())->supportsParser($this->prophesize(NikicPhpParser::class)->reveal()));
    }

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new BasicDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        $this->assertCount(18, $deps);
        $this->assertContains('Foo\Bar:4 on SomeUse', $deps);
        $this->assertContains('Foo\Bar:10 on Foo\SomeParam', $deps);
        $this->assertContains('Foo\Bar:10 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:12 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:13 on SomeOtherClass', $deps);
        $this->assertContains('Foo\Bar:15 on Foo\SomeOtherParam', $deps);
        $this->assertContains('Foo\Bar:19 on Foo\SomeInstanceOf', $deps);
        $this->assertContains('Foo\Bar:21 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:23 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:26 on Some\NamespacedClass', $deps);
        $this->assertContains('Foo\Bar:30 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:32 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:36 on Foo\string2', $deps);
        $this->assertContains('Foo\Bar:38 on string', $deps);
        $this->assertContains('Foo\Bar:40 on string', $deps);
        $this->assertContains('Foo\Bar:42 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:44 on self', $deps);
        $this->assertContains('Foo\Bar:46 on self', $deps);
    }
}
