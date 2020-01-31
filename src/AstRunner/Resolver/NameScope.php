<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

class NameScope
{
    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var array<string, string> alias => className
     */
    private $uses;

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
