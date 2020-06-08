<?php 

namespace Tests\SensioLabs\Deptrac\AstRunner\Fixtures\BasicDependency;

trait BasicDependencyTraitA {}
trait BasicDependencyTraitB {}
trait BasicDependencyTraitC { use BasicDependencyTraitB; }

trait BasicDependencyTraitD {
    use BasicDependencyTraitA;
    use BasicDependencyTraitB;
}

class BasicDependencyTraitClass {
    use BasicDependencyTraitA;
}
