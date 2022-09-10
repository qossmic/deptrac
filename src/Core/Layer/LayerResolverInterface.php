<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;

interface LayerResolverInterface
{
    /**
     * @return array<string, bool> layer name and whether the dependency is public(true) or private(false)
     */
    public function getLayersForReference(TokenReferenceInterface $reference): array;

    public function isReferenceInLayer(string $layer, TokenReferenceInterface $reference): bool;

    public function has(string $layer): bool;
}
