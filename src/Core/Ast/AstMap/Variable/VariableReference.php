<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\Variable;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceMetaDatumInterface;

/**
 * @psalm-immutable
 */
class VariableReference implements TokenReferenceInterface
{
    /**
     * @param TokenReferenceMetaDatumInterface[] $metaData
     */
    public function __construct(
        private readonly SuperGlobalToken $tokenName,
        private readonly array $metaData = []
    ) {}

    public function getFilepath(): ?string
    {
        return null;
    }

    public function getToken(): TokenInterface
    {
        return $this->tokenName;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }
}
