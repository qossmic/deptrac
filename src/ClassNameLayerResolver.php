<?php


namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;

class ClassNameLayerResolver implements ClassNameLayerResolverInterface
{
    /** @var Configuration */
    protected $configuration;

    /** @var AstMap */
    protected $astMap;

    /** @var CollectorFactory */
    protected $collectorFactory;

    /**
     * ClassNameLayerResolver constructor.
     *
     * @param Configuration    $configuration
     * @param AstMap           $astMap
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
        $layers = [];

        foreach ($this->configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                $collector = $this->collectorFactory->getCollector($configurationCollector->getType());

                if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
                    $astClassReference = new AstClassReference($className);
                }

                if ($collector->satisfy(
                    $configurationCollector->getArgs(),
                    $astClassReference,
                    $this->astMap,
                    $this->collectorFactory
                )) {
                    $layers[$configurationLayer->getName()] = true;
                }
            }
        }

        return array_keys($layers);
    }
}
