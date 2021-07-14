<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;

abstract class TokenReferenceBuilder
{
    /** @var string[] */
    protected array $tokenTemplates;

    protected string $filepath;

    /** @var AstDependency[] */
    protected array $dependencies = [];

    /**
     * @param string[] $tokenTemplates
     */
    protected function __construct(array $tokenTemplates, string $filepath)
    {
        $this->tokenTemplates = $tokenTemplates;
        $this->filepath = $filepath;
    }

    /**
     * @return string[]
     */
    final public function getTokenTemplates(): array
    {
        return $this->tokenTemplates;
    }

    public function variable(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::VARIABLE
        );

        return $this;
    }

    public function returnType(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::RETURN_TYPE
        );

        return $this;
    }

    public function throwStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::THROW
        );

        return $this;
    }

    public function anonymousClassExtends(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::ANONYMOUS_CLASS_EXTENDS
        );

        return $this;
    }

    public function constFetch(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::CONST
        );

        return $this;
    }

    public function anonymousClassImplements(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::ANONYMOUS_CLASS_IMPLEMENTS
        );

        return $this;
    }

    public function parameter(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::PARAMETER
        );

        return $this;
    }

}
