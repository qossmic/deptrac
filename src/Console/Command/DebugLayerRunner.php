<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use function array_map;
use function in_array;
use Qossmic\Deptrac\Configuration\ConfigurationLayer;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Exception\Console\InvalidLayerException;
use Qossmic\Deptrac\LayerAnalyser;

/**
 * @internal Should only be used by DebugLayerCommand
 */
final class DebugLayerRunner
{
    private LayerAnalyser $analyser;
    private Loader $loader;

    public function __construct(LayerAnalyser $analyser, Loader $loader)
    {
        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    public function run(DebugLayerOptions $options, Output $output): void
    {
        $configuration = $this->loader->load($options->getConfigurationFile());

        $configurationLayers = array_map(
            static fn (ConfigurationLayer $configurationLayer) => $configurationLayer->getName(),
            $configuration->getLayers()
        );

        if (null !== $options->getLayer() && !in_array($options->getLayer(), $configurationLayers, true)) {
            throw InvalidLayerException::unknownLayer($options->getLayer());
        }

        $layers = null === $options->getLayer() ? $configurationLayers : (array) $options->getLayer();

        foreach ($layers as $layer) {
            $matchedLayers = array_map(
                static fn (string $token) => (array) $token,
                $this->analyser->analyse($configuration, $layer)
            );

            $output->getStyle()->table([$layer], $matchedLayers);
        }
    }
}
