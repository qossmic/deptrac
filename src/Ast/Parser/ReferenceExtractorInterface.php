<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\Parser;

use PhpParser\Node;
use Qossmic\Deptrac\Ast\AstMap\ReferenceBuilder;

interface ReferenceExtractorInterface
{
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void;
}
