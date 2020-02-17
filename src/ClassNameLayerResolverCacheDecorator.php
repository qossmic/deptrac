<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

class ClassNameLayerResolverCacheDecorator implements ClassNameLayerResolverInterface
{
    private $classNameLayerResolver;

    /** @var array<string, string[]> */
    private $layerNamesByClassCache = [];

    /** @var string[] */
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
