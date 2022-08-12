<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser;

class TypeScope
{
    /**
     * @var array<string, string> alias => className
     */
    private array $uses;

    public function __construct(private readonly string $namespace)
    {
        $this->uses = [];
    }

    public function addUse(string $className, ?string $alias): void
    {
        $this->uses[$alias ?: $className] = $className;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array<string, string>
     */
    public function getUses(): array
    {
        return $this->uses;
    }
}
