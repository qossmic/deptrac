<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Contract\Ast\CouldNotParseFileException;
use Qossmic\Deptrac\Contract\Layer\CircularReferenceException;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Contract\Layer\InvalidLayerDefinitionException;
use Qossmic\Deptrac\Contract\Layer\LayerProvider;
use Qossmic\Deptrac\Core\Ast\AstException;
use Qossmic\Deptrac\Core\Ast\AstMapExtractor;
use Qossmic\Deptrac\Core\Dependency\DependencyResolver;
use Qossmic\Deptrac\Core\Dependency\InvalidEmitterConfigurationException;
use Qossmic\Deptrac\Core\Dependency\TokenResolver;
use Qossmic\Deptrac\Core\Dependency\UnrecognizedTokenException;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;

class RulesetUsageAnalyser
{
    /**
     * @param array<array{name:string}> $layers
     */
    public function __construct(
        private readonly LayerProvider $layerProvider,
        private readonly LayerResolverInterface $layerResolver,
        private readonly AstMapExtractor $astMapExtractor,
        private readonly DependencyResolver $dependencyResolver,
        private readonly TokenResolver $tokenResolver,
        private readonly array $layers
    ) {}

    /**
     * @return array<string, array<string, int>>
     *
     * @throws AnalyserException
     */
    public function analyse(): array
    {
        try {
            return $this->findRulesetUsages($this->rulesetResolution());
        } catch (InvalidEmitterConfigurationException $e) {
            throw AnalyserException::invalidEmitterConfiguration($e);
        } catch (UnrecognizedTokenException $e) {
            throw AnalyserException::unrecognizedToken($e);
        } catch (InvalidLayerDefinitionException $e) {
            throw AnalyserException::invalidLayerDefinition($e);
        } catch (InvalidCollectorDefinitionException $e) {
            throw AnalyserException::invalidCollectorDefinition($e);
        } catch (AstException $e) {
            throw AnalyserException::failedAstParsing($e);
        } catch (CouldNotParseFileException $e) {
            throw AnalyserException::couldNotParseFile($e);
        } catch (CircularReferenceException $e) {
            throw AnalyserException::circularReference($e);
        }
    }

    /**
     * @return array<string, array<string, 0>> sourceLayer -> (targetLayer -> 0)
     *
     * @throws \Qossmic\Deptrac\Contract\Layer\CircularReferenceException
     */
    private function rulesetResolution(): array
    {
        $layerNames = [];
        foreach (array_map(
            static fn (array $layerDef): string => $layerDef['name'],
            $this->layers
        ) as $sourceLayerName) {
            foreach (
                $this->layerProvider->getAllowedLayers($sourceLayerName) as $destinationLayerName
            ) {
                $layerNames[$sourceLayerName][$destinationLayerName] = 0;
            }
        }

        return $layerNames;
    }

    /**
     * @param array<string, array<string, 0>> $rulesets
     *
     * @return array<string, array<string, int>>
     *
     * @throws \Qossmic\Deptrac\Core\Ast\AstException
     * @throws \Qossmic\Deptrac\Contract\Ast\CouldNotParseFileException
     * @throws \Qossmic\Deptrac\Core\Dependency\InvalidEmitterConfigurationException
     * @throws \Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException
     * @throws \Qossmic\Deptrac\Contract\Layer\InvalidLayerDefinitionException
     * @throws \Qossmic\Deptrac\Core\Dependency\UnrecognizedTokenException
     */
    private function findRulesetUsages(array $rulesets): array
    {
        $astMap = $this->astMapExtractor->extract();
        $dependencyResult = $this->dependencyResolver->resolve($astMap);
        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $dependerLayerNames = $this->layerResolver->getLayersForReference(
                $this->tokenResolver->resolve($dependency->getDepender(), $astMap),
            );
            foreach ($dependerLayerNames as $dependerLayerName => $_) {
                $dependentLayerNames = $this->layerResolver->getLayersForReference(
                    $this->tokenResolver->resolve($dependency->getDependent(), $astMap),
                );
                foreach ($dependentLayerNames as $dependentLayerName => $__) {
                    if (array_key_exists($dependerLayerName, $rulesets)
                        && array_key_exists($dependentLayerName, $rulesets[$dependerLayerName])
                    ) {
                        ++$rulesets[$dependerLayerName][$dependentLayerName];
                    }
                }
            }
        }

        return $rulesets;
    }
}
