<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\OutputFormatter\Graph\GraphDependency;
use SensioLabs\Deptrac\OutputFormatter\Graphviz\DotWriter;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use SensioLabs\AstRunner\AstMap;
use Symfony\Component\Console\Output\OutputInterface;

class GraphViz2OutputFormatter implements OutputFormatterInterface
{
    protected $eventDispatcher;

    private static $argument_display = 'display';

    private static $argument_dump_image = 'dump-image';

    private static $argument_dump_dot = 'dump-dot';

    private static $argument_dump_html = 'dump-html';

    public function getName()
    {
        return 'graphviz2';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions()
    {
        return [
            OutputFormatterOption::newValueOption(static::$argument_display, 'should try to open graphviz image', true),
            OutputFormatterOption::newValueOption(static::$argument_dump_image, 'path to a dumped png file', ''),
            OutputFormatterOption::newValueOption(static::$argument_dump_dot, 'path to a dumped dot file', ''),
            OutputFormatterOption::newValueOption(static::$argument_dump_html, 'path to a dumped html file', ''),
        ];
    }

    /**
     * @param DependencyContext $dependencyContext
     * @param OutputInterface $output
     * @param OutputFormatterInput $outputFormatterInput
     */
    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ) {;

        $graphDependencies = $this->calculateLayerDependencies(
            $dependencyContext->getViolations(),
            $dependencyContext->getAstMap(),
            $dependencyContext->getDependencyResult(),
            $dependencyContext->getClassNameLayerResolver()
        );



        $g = DotWriter::newDigraph()
            ->writeln("node [shape=box, style=rounded];")
            ->writeln("style=filled;")
        ;

        $g = $this->writeGraphLayers(
            $dependencyContext->getConfiguration()->getLayers(),
            $graphDependencies,
            $g
        );


        echo $g->display();
    }


    /**
     * @param ConfigurationLayer $layer
     * @param GraphDependency[] $grapDependencies
     * @return GraphDependency[]
     */
    private function getGraphDependenciesForLayer(ConfigurationLayer $layer, array $grapDependencies) {
        return array_filter($grapDependencies, function(GraphDependency $graphDependency) use ($layer) {
            return $graphDependency->getLayerA()->getPathname() == $layer->getPathname();
        });
    }

    /**
     * @param RulesetViolation[] $violations
     * @param AstMap $astMap
     * @param DependencyResult $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     * @return Graph\GraphDependency[]
     */
    private function calculateLayerDependencies(
        array $violations,
        AstMap $astMap,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver
    ) {
        /** @var $graphDependency GraphDependency[] */
        $graphDependency = [];


        $d = $dependencyResult->getDependenciesAndInheritDependencies();

        // dependencies
        foreach ($d as $dependency) {
            $layersA = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());
            $layersB = $classNameLayerResolver->getLayersByClassName($dependency->getClassB());

            if (empty($layersB)) {
                continue;
            }

            foreach ($layersA as $layerA) {

                $layerAPathname = $layerA->getPathname();

                foreach ($layersB as $layerB) {

                    $layerBPathname = $layerB->getPathname();

                    if ($layerAPathname == $layerBPathname) {
                        continue;
                    }

                    $uniqueKey = $layerAPathname.'|'.$layerBPathname;

                    if (!isset($graphDependency[$uniqueKey])) {
                        $graphDependency[$uniqueKey] = new GraphDependency($layerA, $layerB);
                    }

                    $graphDependency[$uniqueKey]->addDependency($dependency);
                }
            }
        }

        // violations
        foreach ($violations as $violation) {
            $uniqueKey = $violation->getLayerA()->getPathname().'|'.$violation->getLayerB()->getPathname();
            $graphDependency[$uniqueKey]->addViolation($violation);
        }

        return $graphDependency;
    }

    /**
     * @param ConfigurationLayer[] $layers
     * @param GraphDependency[] $graphDependencies
     * @param DotWriter $dotWriter
     */
    private function writeGraphLayers(array $layers, array $graphDependencies, DotWriter $dotWriter)
    {
        foreach ($layers as $layer) {

            if (!$layer->getLayers()) {
                $this->writeGraphItemLayer($graphDependencies, $dotWriter, $layer);
                continue;
            }

            $layerWriter = DotWriter::newSubgraph($layer);
            $this->writeGraphGroupLayer($graphDependencies, $layerWriter, $layer);
            $dotWriter->writeln($layerWriter);
        }

        return $dotWriter;

    }

    /**
     * @param array $graphDependencies
     * @param DotWriter $dotWriter
     * @param ConfigurationLayer $layer
     */
    private function writeGraphGroupLayer(array $graphDependencies, DotWriter $dotWriter, $layer)
    {
        $layerGraphDependencies = $this->getGraphDependenciesForLayer($layer, $graphDependencies);

        $color = 'gray'.mt_rand(57, 96);
        $dotWriter
            ->writeln("color=" . $color . ";")
            ->writeln(
                'label=<<FONT point-size="19">' . htmlentities(
                    $layer->getName()
                ) . '</FONT><BR/><FONT point-size="8"><FONT color="darkred">3</FONT>/<FONT color="darkred">12</FONT></FONT>>;'
            );

        foreach ($layerGraphDependencies as $layerGraphDependency) {

            if (count($layerGraphDependency->getViolations())) {
                $dotWriter->writeViolationArrow($layerGraphDependency);
                continue;
            }

            $dotWriter->writeArrow($layerGraphDependency);
        }

        $this->writeGraphLayers($layer->getLayers(), $graphDependencies, $dotWriter);
    }

    /**
     * @param array $graphDependencies
     * @param DotWriter $dotWriter
     * @param ConfigurationLayer $layer
     */
    private function writeGraphItemLayer(array $graphDependencies, DotWriter $dotWriter, $layer)
    {
        $layerGraphDependencies = $this->getGraphDependenciesForLayer($layer, $graphDependencies);

        $color = 'gray'.mt_rand(57, 96);

        $dotWriter
           ->writeln(
                '"' . $layer->getPathname() . '" [label=<<FONT point-size="19">' . htmlentities(
                    $layer->getName()
                ) . '</FONT><BR/><FONT point-size="8"><FONT color="darkred">3</FONT>/<FONT color="darkred">12</FONT></FONT>>];'
            );

        foreach ($layerGraphDependencies as $layerGraphDependency) {

            if (count($layerGraphDependency->getViolations())) {
                $dotWriter->writeViolationArrow($layerGraphDependency);
                continue;
            }

            $dotWriter->writeArrow($layerGraphDependency);
        }

        $this->writeGraphLayers($layer->getLayers(), $graphDependencies, $dotWriter);
    }
}
