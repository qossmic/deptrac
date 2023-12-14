<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * A ReferenceBuilder that will ignore all dependencies.
 */
class DisabledReferenceBuilder extends ReferenceBuilder
{
    public function unresolvedFunctionCall(string $functionName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function variable(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function superglobal(string $superglobalName, int $occursAtLine): void
    {
        // no-op
    }

    public function returnType(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function throwStatement(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function anonymousClassExtends(string $classLikeName, int $occursAtLine): void
    {
        // no-op
    }

    public function anonymousClassTrait(string $classLikeName, int $occursAtLine): void
    {
        // no-op
    }

    public function constFetch(string $classLikeName, int $occursAtLine): void
    {
        // no-op
    }

    public function anonymousClassImplements(string $classLikeName, int $occursAtLine): void
    {
        // no-op
    }

    public function parameter(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function attribute(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function instanceof(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function newStatement(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function staticProperty(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function staticMethod(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }

    public function catchStmt(string $classLikeName, int $occursAtLine): self
    {
        // no-op
        return $this;
    }
}
