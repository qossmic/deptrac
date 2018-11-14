<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Dependency\Result;

interface DependencyEmitterInterface
{
    public function getName(): string;

    public function supportsParser(AstParserInterface $astParser): bool;

    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, Result $dependencyResult): void;
}
