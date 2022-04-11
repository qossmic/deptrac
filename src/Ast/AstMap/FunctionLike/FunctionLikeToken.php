<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Ast\AstMap\TokenInterface;

final class FunctionLikeToken implements TokenInterface
{
    private string $functionName;

    private function __construct(string $functionName)
    {
        $this->functionName = $functionName;
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
