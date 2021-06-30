<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

class TypeScope
{
    private string $namespace;

    /**
     * @var array<string, string> alias => className
     */
    private array $uses;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
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
