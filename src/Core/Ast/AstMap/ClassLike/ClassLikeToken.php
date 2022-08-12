<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

final class ClassLikeToken implements TokenInterface
{
    private function __construct(private readonly string $className)
    {
    }

    public static function fromFQCN(string $className): self
    {
        return new self(ltrim($className, '\\'));
    }

    public function match(string $pattern): bool
    {
        return 1 === preg_match($pattern, $this->className);
    }

    //TODO: Replace with String representation (Patrick Kusebauch @ 12.08.22)
    public function toString(): string
    {
        return $this->className;
    }

    public function equals(ClassLikeToken $classLikeName): bool
    {
        return $this->className === $classLikeName->className;
    }

    public function __toString()
    {
        return $this->className;
    }
}
