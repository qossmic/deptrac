<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

final class FileOccurrence
{
    /** @var string */
    private $filepath;

    /** @var int */
    private $line;

    private function __construct(string $filepath, int $line)
    {
        $this->filepath = $filepath;
        $this->line = $line;
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
