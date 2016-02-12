<?php 

namespace DependencyTracker;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;

class ClassNameLayerResolver
{

    protected $classLayerCache = [];

    /** @var Configuration */
    protected $configuration;

    /** @var AstMap */
    protected $astMap;

    /** @var CollectorFactory */
    protected $collectorFactory;

    /**
     * ClassNameLayerResolver constructor.
     * @param Configuration $configuration
     * @param AstMap $astMap
     * @param CollectorFactory $collectorFactory
     */
    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        CollectorFactory $collectorFactory
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorFactory = $collectorFactory;
    }

    public function getLayersByClassName($className)
    {
        if (is_array($cacheHit = $this->getCacheLayersByClass($className))) {
            return $cacheHit;
        }

        foreach ($this->configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {

                $collector = $this->collectorFactory->getCollector($configurationCollector->getType());

                if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
                    // todo print a note that the class is not found!
                    $astClassReference = new AstClassReference($className);
                }

                if ($collector->satisfy(
                    $configurationCollector->getArgs(),
                    $astClassReference,
                    $this->collectorFactory
                )) {
                    $this->addCacheClassToLayer(
                        $astClassReference->getClassName(),
                        $configurationLayer->getName()
                    );
                }
            }
        }

        return $this->getCacheLayersByClass($className, []);
    }

    private function addCacheClassToLayer($class, $layer) {
        if (!isset($this->classLayerCache[$class])) {
            $this->classLayerCache[$class] = [];
        }

        $this->classLayerCache[$class][$layer] = true;
    }

    private function getCacheLayersByClass($class, $default = null) {
        if (!isset($this->classLayerCache[$class])) {
            return $default;
        }

        return array_keys($this->classLayerCache[$class]);
    }


}
