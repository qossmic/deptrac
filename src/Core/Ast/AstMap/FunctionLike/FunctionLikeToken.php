<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

final class FunctionLikeToken implements TokenInterface
{
    private function __construct(private readonly string $functionName)
    {
    }

    public static function fromFQCN(string $functionName): self
    {
        return new self(ltrim($functionName, '\\'));
    }

    public function match(string $pattern): bool
    {
        return 1 === preg_match($pattern, $this->functionName);
    }

    public function toString(): string
    {
        return $this->functionName.'()';
    }

    public function equals(self $functionName): bool
    {
        return $this->functionName === $functionName->functionName;
    }
}
