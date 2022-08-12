<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * @psalm-immutable
 */
final class FileOccurrence
{
    private function __construct(private readonly string $filepath, private readonly int $line)
    {
    }

    public static function fromFilepath(string $filepath, int $occursAtLine): self
    {
        return new self($filepath, $occursAtLine);
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
