<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Layer\Collector\Collectable;
use Qossmic\Deptrac\Layer\Collector\CollectorResolverInterface;
use Qossmic\Deptrac\Layer\Exception\InvalidLayerDefinitionException;
use function array_key_exists;
use function array_unique;
use function array_values;
use function in_array;

class LayerResolver implements LayerResolverInterface
{
    private CollectorResolverInterface $collectorResolver;

    /**
     * @var array<string, Collectable[]>
     */
    private array $layers;

    /**
     * @var array<string, string[]>
     */
    private array $resolved = [];

    /**
     * @param array<array{name?: string, collectors: array<array<string, string|array<string, string>>>}> $layers
     */
    public function __construct(CollectorResolverInterface $collectorResolver, array $layers)
    {
        $this->collectorResolver = $collectorResolver;

        $this->initializeLayers($layers);
    }

    /**
     * @return string[]
     */
    public function getLayersForReference(TokenReferenceInterface $reference, AstMap $astMap): array
    {
        $tokenName = $reference->getToken()->toString();
        if (array_key_exists($tokenName, $this->resolved)) {
            return $this->resolved[$tokenName];
        }

        foreach ($this->layers as $layer => $collectables) {
            foreach ($collectables as $collectable) {
                if ($collectable->getCollector()->resolvable($collectable->getAttributes())) {
                    if ($collectable->getCollector()->satisfy($collectable->getAttributes(), $reference, $astMap)) {
                        $this->resolved[$tokenName][] = $layer;
                    }
                }
            }
        }

        $this->resolved[$tokenName] = array_values(array_unique($this->resolved[$tokenName] ?? []));

        return $this->resolved[$tokenName];
    }

    public function isReferenceInLayer(string $layer, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        $tokenName = $reference->getToken()->toString();
        if (array_key_exists($tokenName, $this->resolved)) {
            return in_array($layer, $this->resolved[$tokenName], true);
        }

        if (!array_key_exists($layer, $this->layers)) {
            return false;
        }

        $collectables = $this->layers[$layer];

        foreach ($collectables as $collectable) {
            if ($collectable->getCollector()->resolvable($collectable->getAttributes())) {
                if ($collectable->getCollector()->satisfy($collectable->getAttributes(), $reference, $astMap)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function has(string $layer): bool
    {
        return array_key_exists($layer, $this->layers);
    }

    /**
     * @param array<array{name?: string, collectors?: array<array<string, string|array<string, string>>>}> $layers
     */
    private function initializeLayers(array $layers): void
    {
        $this->layers = [];
        foreach ($layers as $layer) {
            if (!array_key_exists('name', $layer)) {
                throw InvalidLayerDefinitionException::missingName();
            }

            $layerName = $layer['name'];

            if (array_key_exists($layerName, $this->layers)) {
                throw InvalidLayerDefinitionException::duplicateName($layerName);
            }

            $this->layers[$layerName] = [];
            foreach ($layer['collectors'] ?? [] as $config) {
                $this->layers[$layerName][] = $this->collectorResolver->resolve($config);
            }
            if ([] === $this->layers[$layerName]) {
                throw InvalidLayerDefinitionException::collectorRequired($layerName);
            }
        }

        if ([] === $this->layers) {
            throw InvalidLayerDefinitionException::layerRequired();
        }
    }
}
