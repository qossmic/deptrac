<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Dependency\TokenResolver;
use Qossmic\Deptrac\Layer\LayerResolverInterface;
use function array_values;
use function in_array;
use function natcasesort;

class TokenInLayerAnalyser
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
    public function findTokensInLayer(string $layer): array
    {
        $astMap = $this->astMapExtractor->extract();

        $matchingTokens = [];
        foreach ($astMap->getClassLikeReferences() as $classReference) {
            $classToken = $this->tokenResolver->resolve($classReference->getToken(), $astMap);
            if (in_array($layer, $this->layerResolver->getLayersForReference($classToken, $astMap), true)) {
                $matchingTokens[] = $classToken->getToken()->toString();
            }
        }

        foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
            $functionToken = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
            if (in_array($layer, $this->layerResolver->getLayersForReference($functionToken, $astMap), true)) {
                $matchingTokens[] = $functionToken->getToken()->toString();
            }
        }

        natcasesort($matchingTokens);

        return array_values($matchingTokens);
    }
}
