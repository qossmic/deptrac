<?php

namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;
use SensioLabs\Deptrac\Configuration\ConfigurationLayerInterface;
use SensioLabs\Deptrac\LayerResolver\ResolvedLayer;

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

    private function statisfyConfigurationLayer(ConfigurationLayerInterface $configurationLayer, $className) {

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
     * @param ConfigurationLayerInterface[] $configurationLayers
     * @param $className
     * @return ResolvedLayer[]
     */
    private function getLayersByClassNameRecursive(array $configurationLayers, $className)
    {
        $layers = [];

        foreach ($configurationLayers as $configurationLayer) {
            if (!$this->statisfyConfigurationLayer($configurationLayer, $className)) {
                continue;
            }

            $sublayers = $this->getLayersByClassNameRecursive($configurationLayer->getLayers(), $className);

            $layers = array_merge(
                [$configurationLayer->getPathname() => $sublayers ? ResolvedLayer::newBranch($configurationLayer) : ResolvedLayer::newLeaf($configurationLayer)],
                $sublayers
            );

        }

        return $layers;

    }

    /**
     * @param $className
     * @return ResolvedLayer[]
     */
    public function getLayersByClassName($className)
    {
        return $this->getLayersByClassNameRecursive(
            $this->configuration->getLayers(),
            $className
        );
    }
}
