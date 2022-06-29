<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Node;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;

interface ReferenceExtractorInterface
{
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void;
}
