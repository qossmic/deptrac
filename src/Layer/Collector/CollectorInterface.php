<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

/**
 * A collector is responsible to tell whether an AST node (e.g. a specific class) is part of a layer.
 */
interface CollectorInterface
{
    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool;
}
