<?php

namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;
use SensioLabs\Deptrac\Configuration\ConfigurationLayer;

class ClassNameLayerResolver implements ClassNameLayerResolverInterface
{
    /** @var Configuration */
    protected $configuration;

    /** @var AstMap */
    protected $astMap;

    /** @var CollectorFactory */
    protected $collectorFactory;

    /** @var AstParserInterface */
    protected $astParser;


    /**
     * ClassNameLayerResolver constructor.
     *
     * @param Configuration $configuration
     * @param AstMap $astMap
     * @param CollectorFactory $collectorFactory
     */
    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorFactory = $collectorFactory;
        $this->astParser = $astParser;
    }

    private function statisfyConfigurationLayer(ConfigurationLayer $configurationLayer, $className) {

        if (empty($configurationLayer->getCollectors())) {
            return true;
        }

        foreach ($configurationLayer->getCollectors() as $configurationCollector) {
            $collector = $this->collectorFactory->getCollector($configurationCollector->getType());

            if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
                $astClassReference = new AstClassReference($className);
            }

            if ($collector->satisfy(
                $configurationCollector->getArgs(),
                $astClassReference,
                $this->astMap,
                $this->collectorFactory,
                $this->astParser
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ConfigurationLayer[] $configurationLayers
     * @param $className
     * @return array
     */
    private function getLayersByClassNameRecursive(array $configurationLayers, $className)
    {
        $layers = [];

        foreach ($configurationLayers as $configurationLayer) {
            if (!$this->statisfyConfigurationLayer($configurationLayer, $className)) {
                continue;
            }

            $layers = array_merge(
                [$configurationLayer->getPathname() => $configurationLayer],
                $this->getLayersByClassNameRecursive($configurationLayer->getLayers(), $className)
            );

        }

        return $layers;

    }

    /**
     * @param $className
     * @return ConfigurationLayer[]
     */
    public function getLayersByClassName($className)
    {
        return $this->getLayersByClassNameRecursive(
            $this->configuration->getLayers(),
            $className
        );
    }
}
