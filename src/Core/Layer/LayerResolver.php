<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Layer\Collector\Collectable;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorResolverInterface;
use Qossmic\Deptrac\Core\Layer\Collector\ConditionalCollectorInterface;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;
use function array_key_exists;

class LayerResolver implements LayerResolverInterface
{
    /**
     * @var array<string, Collectable[]>
     */
    private array $layers;

    /**
     * @var array<string, array<string, bool>>
     */
    private array $resolved = [];

    /**
     * @param array<array{name?: string, collectors: array<array<string, string|array<string, string>>>}> $layers
     */
    public function __construct(private readonly CollectorResolverInterface $collectorResolver, array $layers)
    {
        $this->initializeLayers($layers);
    }

    public function getLayersForReference(TokenReferenceInterface $reference, AstMap $astMap): array
    {
        $tokenName = $reference->getToken()->toString();
        if (array_key_exists($tokenName, $this->resolved)) {
            return $this->resolved[$tokenName];
        }

        $this->resolved[$tokenName] = [];

        foreach ($this->layers as $layer => $collectables) {
            foreach ($collectables as $collectable) {
                $collector = $collectable->getCollector();
                $attributes = $collectable->getAttributes();
                if ($collector instanceof ConditionalCollectorInterface
                    && !$collector->resolvable($attributes)
                ) {
                    continue;
                }

                if ($collectable->getCollector()->satisfy($attributes, $reference, $astMap)) {
                    if (array_key_exists($layer, $this->resolved[$tokenName]) && true === $this->resolved[$tokenName][$layer]) {
                        continue;
                    }
                    if (array_key_exists('private', $attributes) && true === $attributes['private']) {
                        $this->resolved[$tokenName][$layer] = false;
                    } else {
                        $this->resolved[$tokenName][$layer] = true;
                    }
                }
            }
        }

        return $this->resolved[$tokenName];
    }

    public function isReferenceInLayer(string $layer, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        $tokenName = $reference->getToken()->toString();
        if (array_key_exists($tokenName, $this->resolved) && [] !== $this->resolved[$tokenName]) {
            return array_key_exists($layer, $this->resolved[$tokenName]);
        }

        if (!array_key_exists($layer, $this->layers)) {
            return false;
        }

        $collectables = $this->layers[$layer];

        foreach ($collectables as $collectable) {
            $collector = $collectable->getCollector();
            if ($collector instanceof ConditionalCollectorInterface
                && !$collector->resolvable($collectable->getAttributes())
            ) {
                continue;
            }

            if ($collector->satisfy($collectable->getAttributes(), $reference, $astMap)) {
                return true;
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
