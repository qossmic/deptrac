<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

interface ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, TypeScope $typeScope): void;
}
