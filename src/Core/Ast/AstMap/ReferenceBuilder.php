<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
abstract class ReferenceBuilder
{
    /** @var DependencyToken[] */
    protected array $dependencies = [];
    /**
     * @param list<string> $tokenTemplates
     */
    protected function __construct(protected array $tokenTemplates, protected string $filepath)
    {
    }
    /**
     * @return string[]
     */
    public final function getTokenTemplates() : array
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
    public function unresolvedFunctionCall(string $functionName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(FunctionToken::fromFQCN($functionName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::UNRESOLVED_FUNCTION_CALL);
        return $this;
    }
    public function variable(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::VARIABLE);
        return $this;
    }
    public function superglobal(string $superglobalName, int $occursAtLine) : void
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(SuperGlobalToken::from($superglobalName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::SUPERGLOBAL_VARIABLE);
    }
    public function returnType(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::RETURN_TYPE);
        return $this;
    }
    public function throwStatement(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::THROW);
        return $this;
    }
    public function anonymousClassExtends(string $classLikeName, int $occursAtLine) : void
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::ANONYMOUS_CLASS_EXTENDS);
    }
    public function anonymousClassTrait(string $classLikeName, int $occursAtLine) : void
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::ANONYMOUS_CLASS_TRAIT);
    }
    public function constFetch(string $classLikeName, int $occursAtLine) : void
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::CONST);
    }
    public function anonymousClassImplements(string $classLikeName, int $occursAtLine) : void
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::ANONYMOUS_CLASS_IMPLEMENTS);
    }
    public function parameter(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::PARAMETER);
        return $this;
    }
    public function attribute(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::ATTRIBUTE);
        return $this;
    }
    public function instanceof(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::INSTANCEOF);
        return $this;
    }
    public function newStatement(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::NEW);
        return $this;
    }
    public function staticProperty(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::STATIC_PROPERTY);
        return $this;
    }
    public function staticMethod(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::STATIC_METHOD);
        return $this;
    }
    public function catchStmt(string $classLikeName, int $occursAtLine) : self
    {
        $this->dependencies[] = new \Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken(ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), DependencyType::CATCH);
        return $this;
    }
    public function addTokenTemplate(string $tokenTemplate) : void
    {
        $this->tokenTemplates[] = $tokenTemplate;
    }
    public function removeTokenTemplate(string $tokenTemplate) : void
    {
        $key = \array_search($tokenTemplate, $this->tokenTemplates, \true);
        if (\false !== $key) {
            unset($this->tokenTemplates[$key]);
        }
    }
}
