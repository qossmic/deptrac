<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\TokenReferenceBuilder;

interface DependencyResolver
{
    public function processNode(Node $node, TokenReferenceBuilder $tokenReferenceBuilder, TypeScope $typeScope): void;
}
