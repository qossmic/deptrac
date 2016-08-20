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

        $this->assertCount(6, $deps);
        $this->assertContains('Foo\Bar:4 on SomeUse', $deps);
        $this->assertContains('Foo\Bar:15 on Foo\SomeParam', $deps);
        $this->assertContains('Foo\Bar:17 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:18 on SomeOtherClass', $deps);
        $this->assertContains('Foo\Bar:20 on Foo\SomeOtherParam', $deps);
        $this->assertContains('Foo\Bar:24 on Foo\SomeInstanceOf', $deps);
    }
}
