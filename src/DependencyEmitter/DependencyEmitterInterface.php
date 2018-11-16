<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Dependency\Result;

interface DependencyEmitterInterface
{
    public function getName(): string;

    public function supportsParser(AstParserInterface $astParser): bool;

    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, Result $dependencyResult): void;
}
