<?php

namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstClassReference;

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
     * @param Configuration    $configuration
     * @param AstMap           $astMap
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

    /**
     * @param string $className
     * @return array
     */
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
                    $this->collectorFactory,
                    $this->astParser
                )
                ) {
                    $layers[$configurationLayer->getName()] = true;
                }
            }
        }

        return array_keys($layers);
    }
}
