<?php


namespace DependencyTracker\Tests\DependencyEmitter;


use DependencyTracker\DependencyEmitter\InheritanceDependencyEmitter;

class InheritanceDependencyEmitterTest extends \PHPUnit_Framework_TestCase
{
    use EmitterTrait;

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new InheritanceDependencyEmitter(),
            new \SplFileInfo(__DIR__ . '/Fixtures/Foo.php')
        );

        $this->assertCount(4, $deps);
        $this->assertContains('Foo\Bar:6 on Foo\BarExtends', $deps);
        $this->assertContains('Foo\Bar:6 on Foo\BarInterface1', $deps);
        $this->assertContains('Foo\Bar:6 on BarInterface2', $deps);
        $this->assertContains('Foo\Bar:8 on Foo\SomeTrait', $deps);

    }

}
