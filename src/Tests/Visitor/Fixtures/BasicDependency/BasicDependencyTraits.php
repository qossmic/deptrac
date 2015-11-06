<?php 

namespace DependencyTracker\Tests\Visitor\Fixtures\BasicDependency;

trait BasicDependencyClassA {}
trait BasicDependencyClassB {}
trait BasicDependencyClassC { use BasicDependencyClassB; }

class BasicDependencyClassD {
    use BasicDependencyClassA;
    use BasicDependencyClassB;
}
