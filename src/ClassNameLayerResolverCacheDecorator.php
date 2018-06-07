<?php

namespace SensioLabs\Deptrac;

class ClassNameLayerResolverCacheDecorator implements ClassNameLayerResolverInterface
{
    private $classNameLayerResolver;
    private $classLayerCache = [];

    public function __construct(ClassNameLayerResolverInterface $classNameLayerResolver)
    {
        $this->classNameLayerResolver = $classNameLayerResolver;
    }

    /**
     * @param string $className
     *
     * @return string[]
     */
    public function getLayersByClassName(string $className): array
    {
        if (!isset($this->classLayerCache[$className])) {
            $this->classLayerCache[$className] = $this->classNameLayerResolver->getLayersByClassName($className);
        }

        return $this->classLayerCache[$className];
    }

    public function getLayers(): array
    {
        return $this->classNameLayerResolver->getLayers();
    }
}
