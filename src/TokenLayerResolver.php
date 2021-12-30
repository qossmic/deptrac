<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstTokenReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\ConfigurationLayer;
use Qossmic\Deptrac\Configuration\ParameterResolver;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;
use RuntimeException;

class TokenLayerResolver implements TokenLayerResolverInterface
{
    private AstMap $astMap;
    private Registry $collectorRegistry;

    /** @var ConfigurationLayer[] */
    private array $resolvedLayerConfiguration;

    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        Registry $collectorRegistry,
        ParameterResolver $parameterResolver
    ) {
        $this->astMap = $astMap;
        $this->collectorRegistry = $collectorRegistry;
        $this->resolvedLayerConfiguration = array_map(static function (ConfigurationLayer $configurationLayer) use (
            $configuration,
            $parameterResolver
        ): ConfigurationLayer {
            return ConfigurationLayer::fromArray(
                $parameterResolver->resolve($configurationLayer->toArray(), $configuration->getParameters())
            );
        }, $configuration->getLayers());
    }

    /**
     * @return string[]
     */
    public function getLayersByTokenName(AstMap\TokenName $tokenName): array
    {
        $astTokenReference = $this->getAstTokenReference($tokenName);

        $layerResolvers = $this->layerResolvers();
        $layerNames = array_map(static fn (ConfigurationLayer $config): string => $config->getName(), $this->resolvedLayerConfiguration);
        /** @var array<string, null> $resolutionTable */
        $resolutionTable = array_combine($layerNames, array_map(static fn ($_) => null, $layerNames));
        $remainingToResolve = count(array_filter($resolutionTable, static fn (?bool $status): bool => null === $status));
        while ($remainingToResolve > 0) {
            foreach ($resolutionTable as $layerName => $isResolved) {
                if (null === $isResolved) {
                    $resolutionTable[$layerName] = $layerResolvers[$layerName]($astTokenReference, $resolutionTable);
                }
            }
            $nowRemaining = count(array_filter($resolutionTable, static fn (?bool $status): bool => null === $status));
            if ($nowRemaining === $remainingToResolve) {
                throw new RuntimeException('Circular dependency between layers detected');
            }
            $remainingToResolve = $nowRemaining;
        }

        return array_keys(array_filter($resolutionTable));
    }

    private function getAstTokenReference(AstMap\TokenName $tokenName): AstTokenReference
    {
        if ($tokenName instanceof ClassLikeName) {
            if (!$astTokenReference = $this->astMap->getClassReferenceByClassName($tokenName)) {
                $astTokenReference = new AstMap\AstClassReference($tokenName);
            }

            return $astTokenReference;
        }

        if ($tokenName instanceof AstMap\FunctionName) {
            if (!$astTokenReference = $this->astMap->getFunctionReferenceByFunctionName($tokenName)) {
                $astTokenReference = new AstMap\AstFunctionReference($tokenName);
            }

            return $astTokenReference;
        }

        if ($tokenName instanceof AstMap\FileName) {
            if (!$astTokenReference = $this->astMap->getFileReferenceByFileName($tokenName)) {
                $astTokenReference = new AstMap\AstFileReference($tokenName->getFilepath(), [], [], []);
            }

            return $astTokenReference;
        }

        if ($tokenName instanceof AstMap\SuperGlobalName) {
            return new AstMap\AstVariableReference($tokenName);
        }

        throw new ShouldNotHappenException();
    }

    /**
     * @return array<string, callable(AstTokenReference, array<string, ?bool>): ?bool>
     */
    private function layerResolvers(): array
    {
        $layerResolvers = [];
        foreach ($this->resolvedLayerConfiguration as $configurationLayer) {
            $layerResolvers[$configurationLayer->getName()] =
                /** @param array<string, ?bool> $resolutionTable */
                function (AstTokenReference $astTokenReference, array $resolutionTable) use (
                    $configurationLayer
                ): ?bool {
                    foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                        $collector = $this->collectorRegistry->getCollector($configurationCollector->getType());
                        if (!$collector->resolvable(
                            $configurationCollector->getArgs(),
                            $this->collectorRegistry,
                            $resolutionTable
                        )
                        ) {
                            return null;
                        }

                        if ($collector->satisfy(
                            $configurationCollector->toArray(),
                            $astTokenReference,
                            $this->astMap,
                            $this->collectorRegistry,
                            $resolutionTable
                        )
                        ) {
                            return true;
                        }
                    }

                    return false;
                };
        }

        return $layerResolvers;
    }
}
