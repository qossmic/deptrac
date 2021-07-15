<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;

class MemoizedClassLikeLayerResolver implements ClassLikeLayerResolverInterface
{
    private ClassLikeLayerResolverInterface $classLikeLayerResolver;

    /** @var array<string, string[]> */
    private array $layerNamesByClassCache = [];

    public function __construct(ClassLikeLayerResolverInterface $classLikeLayerResolver)
    {
        $this->classLikeLayerResolver = $classLikeLayerResolver;
    }

    public function getLayersByClassLikeName(ClassLikeName $className): array
    {
        if (!isset($this->layerNamesByClassCache[$className->toString()])) {
            $this->layerNamesByClassCache[$className->toString()] = $this->classLikeLayerResolver->getLayersByClassLikeName($className);
        }

        return $this->layerNamesByClassCache[$className->toString()];
    }
}
