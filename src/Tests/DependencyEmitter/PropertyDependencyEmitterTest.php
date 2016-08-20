<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\DependencyEmitter\PropertyDependencyEmitter;

class PropertyDependencyEmitterTest extends \PHPUnit_Framework_TestCase
{
    use EmitterTrait;

    public function testGetName()
    {
        $this->assertEquals('PropertyDependencyEmitter', (new PropertyDependencyEmitter())->getName());
    }

    public function testSupportsParser()
    {
        $this->assertTrue((new PropertyDependencyEmitter())->supportsParser($this->prophesize(NikicPhpParser::class)->reveal()));
    }

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new PropertyDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        $this->assertCount(1, $deps);
        $this->assertContains('Foo\Bar:13 on Foo\Bar', $deps);
    }
}
