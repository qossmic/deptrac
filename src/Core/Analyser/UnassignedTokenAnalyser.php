<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Core\Dependency\TokenResolver;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use function array_values;
use function natcasesort;

class UnassignedTokenAnalyser
{
    /**
     * @var array<string>
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
                static function (string $emitterType): ?string {
                    $tokenType = TokenType::tryFromEmitterType($emitterType);

                    return null === $tokenType ? null : $tokenType->value;
                },
                $config['types']
            )
        );
    }

    /**
     * @return string[]
     */
    public function findUnassignedTokens(): array
    {
        $astMap = $this->astMapExtractor->extract();
        $unassignedTokens = [];

        if (in_array(TokenType::CLASS_LIKE, $this->tokenTypes, true)) {
            foreach ($astMap->getClassLikeReferences() as $classReference) {
                $token = $this->tokenResolver->resolve($classReference->getToken(), $astMap);
                $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
                if ([] === $matchingLayers) {
                    $unassignedTokens[] = $classReference->getToken()->toString();
                }
            }
        }

        if (in_array(TokenType::FUNCTION, $this->tokenTypes, true)) {
            foreach ($astMap->getFunctionLikeReferences() as $functionReference) {
                $token = $this->tokenResolver->resolve($functionReference->getToken(), $astMap);
                $matchingLayers = $this->layerResolver->getLayersForReference($token, $astMap);
                if ([] === $matchingLayers) {
                    $unassignedTokens[] = $functionReference->getToken()->toString();
                }
            }
        }

        if (in_array(TokenType::FILE, $this->tokenTypes, true)) {
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
