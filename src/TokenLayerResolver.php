<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\ParameterResolver;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;

class TokenLayerResolver implements TokenLayerResolverInterface
{
    private Configuration $configuration;
    private AstMap $astMap;
    private Registry $collectorRegistry;
    private ParameterResolver $parameterResolver;

    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        Registry $collectorRegistry,
        ParameterResolver $parameterResolver
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorRegistry = $collectorRegistry;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @return string[]
     */
    public function getLayersByTokenName(AstMap\TokenName $tokenName): array
    {
        if ($tokenName instanceof ClassLikeName) {
            if (!$astTokenReference = $this->astMap->getClassReferenceByClassName($tokenName)) {
                $astTokenReference = new AstMap\AstClassReference($tokenName);
            }
        } elseif ($tokenName instanceof AstMap\FunctionName) {
            if (!$astTokenReference = $this->astMap->getFunctionReferenceByFunctionName($tokenName)) {
                $astTokenReference = new AstMap\AstFunctionReference($tokenName);
            }
        } elseif ($tokenName instanceof AstMap\FileName) {
            if (!$astTokenReference = $this->astMap->getFileReferenceByFileName($tokenName)) {
                $astTokenReference = new AstMap\AstFileReference($tokenName->getFilepath(), [], [], []);
            }
        } elseif ($tokenName instanceof AstMap\SuperGlobalName) {
            $astTokenReference = new AstMap\AstVariableReference($tokenName);
        } else {
            throw new ShouldNotHappenException();
        }


        /** @var array<string, bool> $layers */
        $layers = [];

        $layerRegistry = [];
        $numberOfLayersToResolve = count($this->configuration->getLayers());
        $resolvedBeforeLoop = 0;
        while(count($layerRegistry) < $numberOfLayersToResolve) {
            foreach ($this->configuration->getLayers() as $configurationLayer) {
                foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                    $collector = $this->collectorRegistry->getCollector($configurationCollector->getType());

                    $configuration = $this->parameterResolver->resolve(
                        $configurationCollector->getArgs(),
                        $this->configuration->getParameters()
                    );

                    if($collector->resolvable($configuration, $this->collectorRegistry, $layerRegistry)) {
                        if ($collector->satisfy(
                            $configuration,
                            $astTokenReference,
                            $this->astMap,
                            $this->collectorRegistry
                        )
                        ) {
                            $layers[$configurationLayer->getName()] = true;
                        }
                    } else {
                        break 2;
                    }
                }
                $layerRegistry[] = $configurationLayer->getName();
            }
            if($resolvedBeforeLoop === count($layerRegistry)) {
                var_dump($numberOfLayersToResolve);
                throw new \RuntimeException('Circular dependency between layers detected');
            }
            $resolvedBeforeLoop = count($layerRegistry);
        }

        /** @var string[] $layerNames */
        $layerNames = array_keys($layers);

        return $layerNames;
    }
}
