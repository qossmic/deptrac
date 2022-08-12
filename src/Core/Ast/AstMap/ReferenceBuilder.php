<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;

abstract class ReferenceBuilder
{
    /** @var DependencyToken[] */
    protected array $dependencies = [];

    /**
     * @param string[] $tokenTemplates
     */
    protected function __construct(protected array $tokenTemplates, protected string $filepath)
    {
    }

    /**
     * @return string[]
     */
    final public function getTokenTemplates(): array
    {
        return $this->tokenTemplates;
    }

    /**
     * Unqualified function and constant names inside a namespace cannot be
     * statically resolved. Inside a namespace Foo, a call to strlen() may
     * either refer to the namespaced \Foo\strlen(), or the global \strlen().
     * Because PHP-Parser does not have the necessary context to decide this,
     * such names are left unresolved.
     */
    public function unresolvedFunctionCall(string $functionName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            FunctionLikeToken::fromFQCN($functionName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::UNRESOLVED_FUNCTION_CALL
        );

        return $this;
    }

    public function variable(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::VARIABLE
        );

        return $this;
    }

    public function superglobal(string $superglobalName, int $occursAtLine): void
    {
        $this->dependencies[] = new DependencyToken(
            SuperGlobalToken::from($superglobalName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::SUPERGLOBAL_VARIABLE
        );
    }

    public function returnType(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::RETURN_TYPE
        );

        return $this;
    }

    public function throwStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::THROW
        );

        return $this;
    }

    public function anonymousClassExtends(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::ANONYMOUS_CLASS_EXTENDS
        );
    }

    public function anonymousClassTrait(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::ANONYMOUS_CLASS_TRAIT
        );
    }

    public function constFetch(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::CONST
        );
    }

    public function anonymousClassImplements(string $classLikeName, int $occursAtLine): void
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::ANONYMOUS_CLASS_IMPLEMENTS
        );
    }

    public function parameter(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::PARAMETER
        );

        return $this;
    }

    public function attribute(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::ATTRIBUTE
        );

        return $this;
    }

    public function instanceof(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::INSTANCEOF
        );

        return $this;
    }

    public function newStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::NEW
        );

        return $this;
    }

    public function staticProperty(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::STATIC_PROPERTY
        );

        return $this;
    }

    public function staticMethod(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::STATIC_METHOD
        );

        return $this;
    }

    public function catchStmt(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyTokenType::CATCH
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
