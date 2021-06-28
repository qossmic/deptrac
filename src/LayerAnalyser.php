<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Dependency\Resolver;

class LayerAnalyser
{
    private AstRunner $astRunner;
    private FileResolver $fileResolver;
    private Resolver $resolver;
    private ClassLikeLayerResolverFactory $classLikeLayerResolverFactory;

    public function __construct(
        AstRunner $astRunner,
        FileResolver $fileResolver,
        Resolver $resolver,
        ClassLikeLayerResolverFactory $classLikeLayerResolverFactory
    ) {
        $this->astRunner = $astRunner;
        $this->fileResolver = $fileResolver;
        $this->resolver = $resolver;
        $this->classLikeLayerResolverFactory = $classLikeLayerResolverFactory;
    }

    /**
     * @return string[]
     */
    public function analyse(Configuration $configuration, string $layer): array
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration), $configuration->getAnalyzer());
        $dependencyResult = $this->resolver->resolve($astMap);
        $classLikeLayerResolver = $this->classLikeLayerResolverFactory->create($configuration, $astMap);

        /** @var string[] $classLikeNames */
        $classLikeNames = [];

        foreach ($astMap->getAstClassReferences() as $classReference) {
            $classLikeName = $classReference->getTokenName();
            if ($this->isInLayer($layer, $classLikeName, $classLikeLayerResolver)) {
                $classLikeNames[] = $classLikeName->toString();
            }
        }

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $classLikeName = $dependency->getTokenNameA();
            if ($classLikeName instanceof ClassLikeName && $this->isInLayer($layer, $classLikeName, $classLikeLayerResolver)) {
                $classLikeNames[] = $classLikeName->toString();
            }

            $classLikeName = $dependency->getTokenNameB();
            if ($classLikeName instanceof ClassLikeName && $this->isInLayer($layer, $classLikeName, $classLikeLayerResolver)) {
                $classLikeNames[] = $classLikeName->toString();
            }
        }

        $classLikeNames = array_unique($classLikeNames);
        natcasesort($classLikeNames);

        return array_values($classLikeNames);
    }

    private function isInLayer(
        string $layer,
        ClassLikeName $classLikeName,
        ClassLikeLayerResolverInterface $classLikeLayerResolver
    ): bool {
        return in_array($layer, $classLikeLayerResolver->getLayersByClassLikeName($classLikeName), true);
    }
}
