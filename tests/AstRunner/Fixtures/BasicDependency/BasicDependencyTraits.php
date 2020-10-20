<?php 

namespace Tests\SensioLabs\Deptrac\AstRunner\Visitor\Fixtures\BasicDependency;

trait BasicDependencyTraitA {}
trait BasicDependencyTraitB {}
trait BasicDependencyTraitC { use BasicDependencyTraitB; }

trait BasicDependencyTraitD {
    use BasicDependencyTraitA;
    use BasicDependencyTraitB;
}

final class BasicDependencyTraitClass {
    use BasicDependencyTraitA;
}
