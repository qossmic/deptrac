<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Console\Exception\InvalidTokenException;
use Qossmic\Deptrac\Dependency\TokenResolver;
use Qossmic\Deptrac\Layer\LayerResolverInterface;
use function array_values;
use function ksort;
use function natcasesort;
use function str_contains;

class LayerForTokenAnalyser
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
     * @return array<string, string[]>
     */
    public function findLayerForToken(string $tokenName, string $tokenType): array
    {
        $astMap = $this->astMapExtractor->extract();

        switch ($tokenType) {
            case 'class-like':
                return $this->findLayersForReferences($astMap->getClassLikeReferences(), $tokenName, $astMap);
            case 'function':
                return $this->findLayersForReferences($astMap->getFunctionLikeReferences(), $tokenName, $astMap);
            case 'file':
                return $this->findLayersForReferences($astMap->getFileReferences(), $tokenName, $astMap);
        }

        throw InvalidTokenException::invalidTokenType($tokenType, ['class-like', 'function', 'file']);
    }

    /**
     * @param TokenReferenceInterface[] $references
     *
     * @return array<string, string[]>
     */
    private function findLayersForReferences(array $references, string $tokenName, AstMap $astMap): array
    {
        if ([] === $references) {
            return [];
        }

        $layersForReference = [];
        foreach ($references as $reference) {
            if (!str_contains($reference->getToken()->toString(), $tokenName)) {
                continue;
            }
            $token = $this->tokenResolver->resolve($reference->getToken(), $astMap);
            $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);

            natcasesort($matchingLayers);

            $layersForReference[$reference->getToken()->toString()] = array_values($matchingLayers);
        }

        ksort($layersForReference);

        return $layersForReference;
    }
}
