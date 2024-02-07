<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

/**
 * @psalm-immutable
 *
 * Context of the dependency.
 *
 * Any additional info about where the dependency occurred.
 */
final class DependencyContext
{
    public function __construct(
        public readonly FileOccurrence $fileOccurrence,
        public readonly DependencyType $dependencyType
    ) {}
}
