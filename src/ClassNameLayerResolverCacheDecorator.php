<?php

namespace SensioLabs\Deptrac;

class ClassNameLayerResolverCacheDecorator implements ClassNameLayerResolverInterface
{
    private $classNameLayerResolver;

    private $layerNamesByClassCache = [];
    private $layerNamesCache = [];

    public function __construct(ClassNameLayerResolverInterface $classNameLayerResolver)
    {
        $this->classNameLayerResolver = $classNameLayerResolver;
    }

    public function getLayersByClassName(string $className): array
    {
        if (!isset($this->layerNamesByClassCache[$className])) {
            $this->layerNamesByClassCache[$className] = $this->classNameLayerResolver->getLayersByClassName($className);
        }

        return $this->layerNamesByClassCache[$className];
    }

    public function getLayers(): array
    {
        if (empty($this->layerNamesCache)) {
            $this->layerNamesCache = $this->classNameLayerResolver->getLayers();
        }

        return $this->layerNamesCache;
    }
}
