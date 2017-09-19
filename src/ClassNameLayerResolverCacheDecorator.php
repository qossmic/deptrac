<?php

namespace SensioLabs\Deptrac;

class ClassNameLayerResolverCacheDecorator implements ClassNameLayerResolverInterface
{
    /** @var ClassNameLayerResolver */
    private $classNameLayerResolver;

    private $classLayerCache = [];

    /**
     * ClassNameLayerResolverCacheDecorator constructor.
     *
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     */
    public function __construct(ClassNameLayerResolverInterface $classNameLayerResolver)
    {
        $this->classNameLayerResolver = $classNameLayerResolver;
    }

    /**
     * @param string $className
     * @return array
     */
    public function getLayersByClassName($className)
    {
        if (!isset($this->classLayerCache[$className])) {
            $this->classLayerCache[$className] = $this->classNameLayerResolver->getLayersByClassName($className);
        }

        return $this->classLayerCache[$className];
    }
}
