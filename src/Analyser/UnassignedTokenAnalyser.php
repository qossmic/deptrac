<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Dependency\TokenResolver;
use Qossmic\Deptrac\Layer\LayerResolverInterface;
use function array_values;
use function natcasesort;

class UnassignedTokenAnalyser
{
    private AstMapExtractor $astMapExtractor;
    private TokenResolver $tokenResolver;
    private LayerResolverInterface $layerResolver;

    public function __construct(
        AstMapExtractor $astMapExtractor,
        TokenResolver $tokenResolver,
        LayerResolverInterface $layerResolver
    ) {
        $this->astMapExtractor = $astMapExtractor;
        $this->tokenResolver = $tokenResolver;
        $this->layerResolver = $layerResolver;
    }

    /**
     * @return string[]
     */
    public function findUnassignedTokens(): array
    {
        $astMap = $this->astMapExtractor->extract();
        $unassignedTokens = [];

        foreach ($astMap->getClassLikeReferences() as $classReference) {
            $token = $this->tokenResolver->resolve($classReference->getToken(), $astMap);
            $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
            if ([] === $matchingLayers) {
                $unassignedTokens[] = $classReference->getToken()->toString();
            }
        }

        foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
            $token = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
            $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
            if ([] === $matchingLayers) {
                $unassignedTokens[] = $functionReference->getToken()->toString();
            }
        }

        natcasesort($unassignedTokens);

        return array_values($unassignedTokens);
    }
}
