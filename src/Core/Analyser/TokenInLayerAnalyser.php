<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Core\Dependency\TokenResolver;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterType;
use function array_values;
use function in_array;
use function natcasesort;

class TokenInLayerAnalyser
{
    /**
     * @var array<TokenType>
     */
    private readonly array $tokenTypes;

    /**
     * @param array{types: array<string>} $config
     */
    public function __construct(
        private readonly AstMapExtractor $astMapExtractor,
        private readonly TokenResolver $tokenResolver,
        private readonly LayerResolverInterface $layerResolver,
        array $config
    ) {
        $this->tokenTypes = array_filter(
            array_map(
                static fn (string $emitterType): ?TokenType => TokenType::tryFromEmitterType(EmitterType::from($emitterType)),
                $config['types']
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
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($classToken))) {
                    $matchingTokens[] = $classToken->getToken()
                        ->toString();
                }
            }
        }

        if (in_array(TokenType::FUNCTION, $this->tokenTypes, true)) {
            foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
                $functionToken = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($functionToken))) {
                    $matchingTokens[] = $functionToken->getToken()
                        ->toString();
                }
            }
        }

        if (in_array(TokenType::FILE, $this->tokenTypes, true)) {
            foreach ($astMap->getFileReferences() as $fileReference) {
                $fileToken = $this->tokenResolver->resolve($fileReference->getToken(), $astMap);
                if (array_key_exists($layer, $this->layerResolver->getLayersForReference($fileToken))) {
                    $matchingTokens[] = $fileToken->getToken()
                        ->toString();
                }
            }
        }

        natcasesort($matchingTokens);

        return array_values($matchingTokens);
    }
}
