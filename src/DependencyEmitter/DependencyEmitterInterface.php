<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

interface DependencyEmitterInterface
{
    public function getName(): string;

    public function supportsParser(AstParserInterface $astParser): bool;

    /**
     * @param AstParserInterface $astParser
     * @param AstMap             $astMap
     * @param DependencyResult   $dependencyResult
     *
     * @return mixed
     */
    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, DependencyResult $dependencyResult);
}
