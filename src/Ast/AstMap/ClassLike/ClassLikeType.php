<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Layer\Collector\CollectorTypes;

final class ClassLikeType implements TokenInterface
{
    private string $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function classLike(): self
    {
        return new self(CollectorTypes::TYPE_CLASSLIKE);
    }

    public static function class(): self
    {
        return new self(CollectorTypes::TYPE_CLASS);
    }

    public static function interface(): self
    {
        return new self(CollectorTypes::TYPE_INTERFACE);
    }

    public static function trait(): self
    {
        return new self(CollectorTypes::TYPE_TRAIT);
    }

    public function matches(ClassLikeType $type): bool
    {
        return $this->toString() === $type->toString();
    }

    public function toString(): string
    {
        return $this->type;
    }
}
