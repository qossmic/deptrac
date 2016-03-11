<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

interface DependencyEmitterInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param AstParserInterface $astParser
     *
     * @return bool
     */
    public function supportsParser(AstParserInterface $astParser);

    /**
     * @param AstParserInterface $astParser
     * @param AstMap             $astMap
     * @param DependencyResult   $dependencyResult
     *
     * @return mixed
     */
    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, DependencyResult $dependencyResult);
}
