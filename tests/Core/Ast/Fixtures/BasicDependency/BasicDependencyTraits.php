<?php

namespace Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency;

trait BasicDependencyTraitA {}
trait BasicDependencyTraitB {}
trait BasicDependencyTraitC { use \Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicDependency\BasicDependencyTraitB; }

trait BasicDependencyTraitD {
    use BasicDependencyTraitA;
    use BasicDependencyTraitB;
}

final class BasicDependencyTraitClass {
    use BasicDependencyTraitA;
}
