<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Dependency\DependencyResolver;
use Qossmic\Deptrac\Dependency\Emitter\EmitterTypes;
use Qossmic\Deptrac\Dependency\TokenResolver;
use Qossmic\Deptrac\Layer\LayerResolverInterface;
use function array_values;
use function natcasesort;

class UnassignedTokenAnalyser
{
    private AstMapExtractor $astMapExtractor;
    private TokenResolver $tokenResolver;
    private LayerResolverInterface $layerResolver;

    /**
     * @var array{types: array<string>}
     */
    private array $config;

    /**
     * @param array{types?: array<string>} $config
     */
    public function __construct(
        AstMapExtractor $astMapExtractor,
        TokenResolver $tokenResolver,
        LayerResolverInterface $layerResolver,
        array $config
    ) {
        $this->astMapExtractor = $astMapExtractor;
        $this->tokenResolver = $tokenResolver;
        $this->layerResolver = $layerResolver;
        $this->config = array_merge(DependencyResolver::DEFAULT_EMITTERS, $config);
    }

    /**
     * @return string[]
     */
    public function findUnassignedTokens(): array
    {
        $astMap = $this->astMapExtractor->extract();
        $unassignedTokens = [];

        if (in_array(EmitterTypes::CLASS_TOKEN, $this->config['types'], true)) {
            foreach ($astMap->getClassLikeReferences() as $classReference) {
                $token = $this->tokenResolver->resolve($classReference->getToken(), $astMap);
                $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
                if ([] === $matchingLayers) {
                    $unassignedTokens[] = $classReference->getToken()->toString();
                }
            }
        }

        if (in_array(EmitterTypes::FUNCTION_TOKEN, $this->config['types'], true)) {
            foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
                $token = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
                $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
                if ([] === $matchingLayers) {
                    $unassignedTokens[] = $functionReference->getToken()->toString();
                }
            }
        }

        if (in_array(EmitterTypes::FILE_TOKEN, $this->config['types'], true)) {
            foreach ($astMap->getFileReferences() as $fileReference) {
                $token = $this->tokenResolver->resolve($fileReference->getToken(), $astMap);
                $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
                if ([] === $matchingLayers) {
                    $unassignedTokens[] = $fileReference->getToken()->toString();
                }
            }
        }

        natcasesort($unassignedTokens);

        return array_values($unassignedTokens);
    }
}
