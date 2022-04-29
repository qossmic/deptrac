<?php

namespace Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency;

trait BasicDependencyTraitA {}
trait BasicDependencyTraitB {}
trait BasicDependencyTraitC { use \Tests\Qossmic\Deptrac\Ast\Fixtures\BasicDependency\BasicDependencyTraitB; }

trait BasicDependencyTraitD {
    use BasicDependencyTraitA;
    use BasicDependencyTraitB;
}

final class BasicDependencyTraitClass {
    use BasicDependencyTraitA;
}
