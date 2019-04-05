<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;

interface ClassDependencyResolver
{
    public function processNode(Node $node, AstClassReference $astClassReference): void;
}
