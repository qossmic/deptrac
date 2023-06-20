<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures;

/**
 * @method ClassDocBlockDependencyBrother test(ClassDocBlockDependencySister $sister)
 *
 * @property ClassDocBlockDependencyChild $child
 * @property-read ClassDocBlockDependencySister $sister
 * @property-write ClassDocBlockDependencyBrother $brother
 */
final class ClassDocBlockDependency
{
}

final class ClassDocBlockDependencyChild
{
}

final class ClassDocBlockDependencyBrother
{
}

final class ClassDocBlockDependencySister
{
}
