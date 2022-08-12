<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * @psalm-immutable
 */
final class FileOccurrence
{
    private function __construct(
        public readonly string $filepath,
        public readonly int $line
    ) {
    }

    public static function fromFilepath(string $filepath, int $occursAtLine): self
    {
        return new self($filepath, $occursAtLine);
    }
}
