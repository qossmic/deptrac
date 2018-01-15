<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

class EmittedDependency
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
