<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * @psalm-immutable
 */
final class FileOccurrence
{
    public function __construct(
        public readonly string $filepath,
        public readonly int $line
    ) {
    }
}
