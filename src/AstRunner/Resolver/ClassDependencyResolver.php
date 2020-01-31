<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

interface ClassDependencyResolver
{
    public function processNode(Node $node, AstFileReference $fileReference, AstClassReference $astClassReference, NameScope $nameScope): void;
}
