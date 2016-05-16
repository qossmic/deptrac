<?php

namespace SensioLabs\Deptrac\LayerResolver;

use SensioLabs\Deptrac\Configuration\ConfigurationCollector;
use SensioLabs\Deptrac\Configuration\ConfigurationLayerInterface;

class ResolvedLayer implements ConfigurationLayerInterface
{
    /** @var ConfigurationLayerInterface */
    private $configurationLayer;

    /** @var bool */
    private $leaf;

    /**
     * @param ConfigurationLayerInterface $configurationLayer
     * @param bool $leaf
     */
    public function __construct(ConfigurationLayerInterface $configurationLayer, $leaf)
    {
        $this->configurationLayer = $configurationLayer;
        $this->leaf = $leaf;
    }

    /**
     * @param ConfigurationLayerInterface $configurationLayer
     * @return static
     */
    public static function newLeaf(ConfigurationLayerInterface $configurationLayer)
    {
        return new static($configurationLayer, true);
    }

    /**
     * @param ConfigurationLayerInterface $configurationLayer
     * @return static
     */
    public static function newBranch(ConfigurationLayerInterface $configurationLayer)
    {
        return new static($configurationLayer, false);
    }

    /** @return ConfigurationCollector[] */
    public function getCollectors()
    {
        return $this->configurationLayer->getCollectors();
    }

    public function getName()
    {
        return $this->configurationLayer->getName();
    }

    public function getPathname()
    {
        return $this->configurationLayer->getPathname();
    }

    public function getLayers()
    {
        return $this->configurationLayer->getLayers();
    }

    public function getId()
    {
        return $this->configurationLayer->getId();
    }

    public function isLeaf()
    {
        return $this->leaf;
    }

}
