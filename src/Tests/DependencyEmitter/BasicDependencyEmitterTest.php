<?php


namespace DependencyTracker\Tests\DependencyEmitter;


use DependencyTracker\DependencyEmitter\BasicDependencyEmitter;

class BasicDependencyEmitterTest extends \PHPUnit_Framework_TestCase
{

    use EmitterTrait;

    public function testApplyDependencies()
    {
        $deps = $this->getDeps(
            new BasicDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        $this->assertCount(6, $deps);
        $this->assertContains('Foo\Bar:4 on SomeUse', $deps);
        $this->assertContains('Foo\Bar:10 on Foo\SomeParam', $deps);
        $this->assertContains('Foo\Bar:12 on Foo\SomeClass', $deps);
        $this->assertContains('Foo\Bar:13 on SomeOtherClass', $deps);
        $this->assertContains('Foo\Bar:15 on Foo\SomeOtherParam', $deps);
        $this->assertContains('Foo\Bar:19 on Foo\SomeInstanceOf', $deps);
    }

}
