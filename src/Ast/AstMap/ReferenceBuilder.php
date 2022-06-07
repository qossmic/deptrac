<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap;

use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\Variable\SuperGlobalToken;

abstract class ReferenceBuilder
{
    /** @var string[] */
    protected array $tokenTemplates;

    protected string $filepath;

    /** @var DependencyToken[] */
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
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::VARIABLE
        );

        return $this;
    }

    public function superglobal(string $superglobalName, int $occursAtLine): void
    {
        $this->dependencies[] = DependencyToken::fromType(
            new SuperGlobalToken($superglobalName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::SUPERGLOBAL_VARIABLE
        );
    }

    public function returnType(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::RETURN_TYPE
        );

        return $this;
    }

    public function throwStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::THROW
        );

        return $this;
    }

    public function anonymousClassExtends(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::ANONYMOUS_CLASS_EXTENDS
        );
    }

    public function anonymousClassTrait(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::ANONYMOUS_CLASS_TRAIT
        );
    }

    public function constFetch(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::CONST
        );
    }

    public function anonymousClassImplements(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::ANONYMOUS_CLASS_IMPLEMENTS
        );
    }

    public function parameter(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::PARAMETER
        );

        return $this;
    }

    public function attribute(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::ATTRIBUTE
        );

        return $this;
    }

    public function instanceof(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::INSTANCEOF
        );

        return $this;
    }

    public function newStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::NEW
        );

        return $this;
    }

    public function staticProperty(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::STATIC_PROPERTY
        );

        return $this;
    }

    public function staticMethod(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::STATIC_METHOD
        );

        return $this;
    }

    public function catchStmt(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::CATCH
        );

        return $this;
    }

    public function addTokenTemplate(string $tokenTemplate): void
    {
        $this->tokenTemplates[] = $tokenTemplate;
    }

    public function removeTokenTemplate(string $tokenTemplate): void
    {
        $key = array_search($tokenTemplate, $this->tokenTemplates, true);
        if (false === $key) {
            return;
        }
        unset($this->tokenTemplates[$key]);
    }
}
