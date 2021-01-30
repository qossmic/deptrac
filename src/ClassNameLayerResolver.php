<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;

class ClassNameLayerResolver implements ClassNameLayerResolverInterface
{
    private $configuration;
    private $astMap;
    private $collectorRegistry;

    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        Registry $collectorRegistry
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorRegistry = $collectorRegistry;
    }

    /**
     * @return string[]
     */
    public function getLayersByClassName(ClassLikeName $className): array
    {
        /** @var array<string, bool> $layers */
        $layers = [];

        if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
            $astClassReference = new AstClassReference($className);
        }

        foreach ($this->configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                $collector = $this->collectorRegistry->getCollector($configurationCollector->getType());

                if ($collector->satisfy(
                    $configurationCollector->getArgs(),
                    $astClassReference,
                    $this->astMap,
                    $this->collectorRegistry
                )) {
                    $layers[$configurationLayer->getName()] = true;
                }
            }
        }

        /** @var string[] $layerNames */
        $layerNames = array_keys($layers);

        return $layerNames;
    }
}
