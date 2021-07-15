<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\ParameterResolver;

class ClassLikeLayerResolver implements ClassLikeLayerResolverInterface
{
    private Configuration $configuration;
    private AstMap $astMap;
    private Registry $collectorRegistry;
    private ParameterResolver $parameterResolver;

    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        Registry $collectorRegistry,
        ParameterResolver $parameterResolver
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorRegistry = $collectorRegistry;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @return string[]
     */
    public function getLayersByClassLikeName(ClassLikeName $className): array
    {
        /** @var array<string, bool> $layers */
        $layers = [];

        if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
            $astClassReference = new AstMap\ClassToken\AstClassReference($className);
        }

        foreach ($this->configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                $collector = $this->collectorRegistry->getCollector($configurationCollector->getType());

                $configuration = $this->parameterResolver->resolve(
                    $configurationCollector->getArgs(),
                    $this->configuration->getParameters()
                );

                if ($collector->satisfy($configuration, $astClassReference, $this->astMap, $this->collectorRegistry)) {
                    $layers[$configurationLayer->getName()] = true;
                }
            }
        }

        /** @var string[] $layerNames */
        $layerNames = array_keys($layers);

        return $layerNames;
    }
}
