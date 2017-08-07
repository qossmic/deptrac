<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

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
        $this->assertContains('Foo\Bar:4 [use] on SomeUse', $deps);
        $this->assertContains('Foo\Bar:10 [parameter] on Foo\SomeParam', $deps);
        $this->assertContains('Foo\Bar:10 [return] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:12 [new] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:13 [new] on SomeOtherClass', $deps);
        $this->assertContains('Foo\Bar:15 [parameter] on Foo\SomeOtherParam', $deps);
        $this->assertContains('Foo\Bar:19 [instanceof] on Foo\SomeInstanceOf', $deps);
        $this->assertContains('Foo\Bar:21 [static_method] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:23 [static_property] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:26 [return] on Some\NamespacedClass', $deps);
        $this->assertContains('Foo\Bar:30 [return] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:32 [return] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:36 [return] on Foo\string2', $deps);
        $this->assertContains('Foo\Bar:38 [return] on string', $deps);
        $this->assertContains('Foo\Bar:40 [return] on string', $deps);
        $this->assertContains('Foo\Bar:42 [return] on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:44 [return] on self', $deps);
        $this->assertContains('Foo\Bar:46 [return] on self', $deps);
    }
}
