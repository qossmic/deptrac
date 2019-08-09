<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstRunner;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Dependency\Resolver;

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

    public function analyse(Configuration $configuration): DependencyContext
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration));
        $dependencyResult = $this->resolver->resolve($astMap);

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorRegistry)
        );

        /** @var RulesetEngine\RulesetViolation[] $violations */
        $violations = $this->rulesetEngine->getViolations(
            $dependencyResult,
            $classNameLayerResolver,
            $configuration->getRuleset()
        );

        $skippedViolations = $this->rulesetEngine->getSkippedViolations(
            $violations,
            $configuration->getSkipViolations()
        );

        return new DependencyContext(
            $astMap,
            $dependencyResult,
            $classNameLayerResolver,
            $violations,
            $skippedViolations
        );
    }
}
