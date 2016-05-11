<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\OutputFormatter\Graph\GraphDependency;
use SensioLabs\Deptrac\OutputFormatter\Graphviz\DotWriter;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Fhaculty\Graph\Vertex;
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
            ->writeln("color=lightgrey;")
        ;

        $g = $this->writeGraphLayers(
            $dependencyContext->getConfiguration()->getLayers(),
            $graphDependencies,
            $g
        );


        echo $g->render();


        return;

        if ($outputFormatterInput->getOption(static::$argument_display)) {
            (new \Graphp\GraphViz\GraphViz())->display($graph);
        }

        if ($dumpImagePath = $outputFormatterInput->getOption(static::$argument_dump_image)) {
            $imagePath = (new \Graphp\GraphViz\GraphViz())->createImageFile($graph);
            rename($imagePath, $dumpImagePath);
            $output->writeln('<info>Image dumped to '.realpath($dumpImagePath).'</info>');
        }

        if ($dumpDotPath = $outputFormatterInput->getOption(static::$argument_dump_dot)) {
            file_put_contents($dumpDotPath, (new \Graphp\GraphViz\GraphViz())->createScript($graph));
            $output->writeln('<info>Script dumped to '.realpath($dumpDotPath).'</info>');
        }

        if ($dumpHtmlPath = $outputFormatterInput->getOption(static::$argument_dump_html)) {
            file_put_contents($dumpHtmlPath, (new \Graphp\GraphViz\GraphViz())->createImageHtml($graph));
            $output->writeln('<info>HTML dumped to '.realpath($dumpHtmlPath).'</info>');
        }
    }

    /**
     * @param ConfigurationLayer[] $layers
     * @param GraphDependency[] $graphDependencies
     * @param DotWriter $dotWriter
     */
    private function writeGraphLayers(array $layers, array $graphDependencies, DotWriter $dotWriter)
    {
        foreach ($layers as $layer) {

            $layerGraphDependencies = $this->getGraphDependenciesForLayer($layer, $graphDependencies);

            if (!count($layerGraphDependencies)) {
                continue;
            }

            $dotWriter
                ->writeln(
                    '"' . $layer->getPathname() . '" [label=<<FONT point-size="19">' . $layer->getPathname() . '</FONT><BR/><FONT point-size="8"><FONT color="darkred">3</FONT>/<FONT color="darkred">12</FONT></FONT>>];'
                );

            foreach ($layerGraphDependencies as $layerGraphDependency) {

                if (count($layerGraphDependency->getViolations())) {
                    $dotWriter
                        ->writeln(
                            '"' . $layerGraphDependency->getLayerA()->getPathname() . '" -> "' . $layerGraphDependency->getLayerB()->getPathname() . '" [label = "' . count($layerGraphDependency->getViolations()) . '", color=red ];'
                        );
                    continue;
                }

                $dotWriter
                    ->writeln(
                        '"' . $layerGraphDependency->getLayerA()->getPathname() . '" -> "' . $layerGraphDependency->getLayerB() . '";'
                    );
            }

            $dotWriter->writeln($this->writeGraphLayers($layer->getLayers(), $graphDependencies, DotWriter::newSubgraph()));
        }

        return $dotWriter;

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

        // all classes
        /*
        TODO, drap all layers...
        foreach ($astMap->getAstClassReferences() as $classReferences) {
            foreach ($classNameLayerResolver->getLayersByClassName($classReferences->getClassName()) as $classReferenceLayer) {
                $layersDependOnLayers[$classReferenceLayer->getPathname()] = [];
            }
        }*/

        // dependencies
        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
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
}
