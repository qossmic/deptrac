<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Dependency\DependencyResolver;
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

    /**
     * @var array<string>
     */
    private array $tokenTypes;

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
        $emitters = array_merge(DependencyResolver::DEFAULT_EMITTERS, $config);
        $this->tokenTypes = array_filter(
            array_map(
                static function (string $emitterType): ?string {
                    $tokenType = TokenType::tryFromEmitterType($emitterType);

                    return null === $tokenType ? null : $tokenType->value;
                },
                $emitters['types']
            )
        );
    }

    /**
     * @return string[]
     */
    public function findTokensInLayer(string $layer): array
    {
        $astMap = $this->astMapExtractor->extract();

        $matchingTokens = [];

        if (in_array(TokenType::CLASS_LIKE, $this->tokenTypes, true)) {
            foreach ($astMap->getClassLikeReferences() as $classReference) {
                $classToken = $this->tokenResolver->resolve($classReference->getToken(), $astMap);
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($classToken, $astMap))) {
                    $matchingTokens[] = $classToken->getToken()
                        ->toString();
                }
            }
        }

        if (in_array(TokenType::FUNCTION, $this->tokenTypes, true)) {
            foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
                $functionToken = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($functionToken, $astMap))) {
                    $matchingTokens[] = $functionToken->getToken()
                        ->toString();
                }
            }
        }

        if (in_array(TokenType::FILE, $this->tokenTypes, true)) {
            foreach ($astMap->getFileReferences() as $fileReference) {
                $fileToken = $this->tokenResolver->resolve($fileReference->getToken(), $astMap);
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($fileToken, $astMap))) {
                    $matchingTokens[] = $fileToken->getToken()
                        ->toString();
                }
            }
        }

        natcasesort($matchingTokens);

        return array_values($matchingTokens);
    }
}
