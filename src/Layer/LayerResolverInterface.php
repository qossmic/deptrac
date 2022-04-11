<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

interface LayerResolverInterface
{
    /**
     * @return string[]
     */
    public function getLayersForReference(TokenReferenceInterface $reference, AstMap $astMap): array;

    public function isReferenceInLayer(string $layer, TokenReferenceInterface $reference, AstMap $astMap): bool;

    public function has(string $layer): bool;
}
