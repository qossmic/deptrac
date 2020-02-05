<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;

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

    public function getLayersByClassName(ClassLikeName $className): array
    {
        if (!isset($this->layerNamesByClassCache[$className->toString()])) {
            $this->layerNamesByClassCache[$className->toString()] = $this->classNameLayerResolver->getLayersByClassName($className);
        }

        return $this->layerNamesByClassCache[$className->toString()];
    }

    public function getLayers(): array
    {
        if (empty($this->layerNamesCache)) {
            $this->layerNamesCache = $this->classNameLayerResolver->getLayers();
        }

        return $this->layerNamesCache;
    }
}
