<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

interface ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void;
}
