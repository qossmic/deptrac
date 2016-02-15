<?php 

namespace DependencyTracker;

class ClassNameLayerResolverCacheDecorator implements ClassNameLayerResolverInterface
{
    /** @var ClassNameLayerResolver */
    private $classNameLayerResolver;

    private $classLayerCache = [];

    /**
     * ClassNameLayerResolverCacheDecorator constructor.
     * @param ClassNameLayerResolver $classNameLayerResolver
     */
    public function __construct(ClassNameLayerResolver $classNameLayerResolver)
    {
        $this->classNameLayerResolver = $classNameLayerResolver;
    }

    public function getLayersByClassName($className)
    {
        if (!isset($this->classLayerCache[$className])) {
            $this->classLayerCache[$className] = $this->classNameLayerResolver->getLayersByClassName($className);
        }

        return $this->classLayerCache[$className];
    }
}
