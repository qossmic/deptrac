<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\Configuration\Configuration;

class UnassignedAnalyser
{
    private $astRunner;
    private $fileResolver;
    private $classLikeLayerResolverFactory;

    public function __construct(
        AstRunner $astRunner,
        FileResolver $fileResolver,
        ClassLikeLayerResolverFactory $classLikeLayerResolverFactory
    ) {
        $this->astRunner = $astRunner;
        $this->fileResolver = $fileResolver;
        $this->classLikeLayerResolverFactory = $classLikeLayerResolverFactory;
    }

    /**
     * @return string[]
     */
    public function analyse(Configuration $configuration): array
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration));
        $classLikeLayerResolver = $this->classLikeLayerResolverFactory->create($configuration, $astMap);

        /** @var string[] $classLikeNames */
        $classLikeNames = [];

        foreach ($astMap->getAstClassReferences() as $classReference) {
            $classLikeName = $classReference->getClassLikeName();
            if ([] === $classLikeLayerResolver->getLayersByClassLikeName($classLikeName)) {
                $classLikeNames[] = $classLikeName->toString();
            }
        }

        natcasesort($classLikeNames);

        return array_values($classLikeNames);
    }
}
