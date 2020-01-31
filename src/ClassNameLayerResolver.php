<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Configuration\ConfigurationLayer;

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
                    clone $astClassReference,
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

    public function getLayers(): array
    {
        return array_map(
            static function (ConfigurationLayer $configurationLayer): string {
                return $configurationLayer->getName();
            },
            $this->configuration->getLayers()
        );
    }
}
