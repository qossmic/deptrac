<?php

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Dependency\Resolver;
use Qossmic\Deptrac\RulesetEngine\Context;

class Analyser
{
    private $astRunner;
    private $fileResolver;
    private $resolver;
    private $collectorRegistry;
    private $rulesetEngine;

    public function __construct(
        AstRunner $astRunner,
        FileResolver $fileResolver,
        Resolver $resolver,
        Registry $collectorRegistry,
        RulesetEngine $rulesetEngine
    ) {
        $this->astRunner = $astRunner;
        $this->fileResolver = $fileResolver;
        $this->resolver = $resolver;
        $this->collectorRegistry = $collectorRegistry;
        $this->rulesetEngine = $rulesetEngine;
    }

    public function analyse(Configuration $configuration): Context
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration));
        $dependencyResult = $this->resolver->resolve($astMap);

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorRegistry)
        );

        return $this->rulesetEngine->process(
            $dependencyResult,
            $classNameLayerResolver,
            $configuration
        );
    }
}
