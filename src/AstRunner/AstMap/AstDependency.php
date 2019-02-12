<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstDependency
{
    private $class;

    private $line;

    private $type;

    public function __construct(string $class, int $line, string $type)
    {
        $this->class = $class;
        $this->line = $line;
        $this->type = $type;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
