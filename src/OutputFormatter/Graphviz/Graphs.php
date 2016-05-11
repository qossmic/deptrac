<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graphviz;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\OutputFormatter\Graph\GraphDependency;

class Graphs
{

    private $style = 'filled';
    private $color = "lightgrey";

    /** @var ConfigurationLayer */
    private $layer;

    /** @var GraphDependency[] */
    private $graphDependencies;

    private $subsGraphs = [];
    private $hasParent = false;

    /**
     * Graphs constructor.
     * @param string $label
     * @param array $path
     * @param array $subsGraphs
     */
    public function __construct($label, array $path, array $subsGraphs, $hasParent)
    {
        $this->label = $label;
        $this->path = $path;
        $this->subsGraphs = $subsGraphs;
        $this->hasParent = $hasParent;
    }

    /**
     * @return string
     * 	node [style=filled];
    b0 -> b1 -> b2 -> b3;
    label = "process #2";
    color=blue
     */
    public function render()
    {

        return
            DotWriter::newDigraph()
                ->writeln("node [shape=box, style=rounded];")
                ->writeln("style=filled;")
                ->writeln("color=lightgrey;")
                ->writeln('a1 [label=<<FONT point-size="19">aa11</FONT><BR/><FONT point-size="8"><FONT color="darkred">6</FONT>/<FONT color="darkred">12</FONT></FONT>>];')
                ->writeln('a1 -> b3 -> b2 -> a3 ->a3 -> a0;')
                ->writeln('a3 -> sub3 [label = " a to b", color=red ];')
                ->writeln(
                    DotWriter::newSubgraph()
                    ->writeln("style=filled;")
                    ->writeln("color=red;")
                    ->writeln('sub1 -> sub2;')
                    ->writeln(
                        DotWriter::newSubgraph()
                            ->writeln("style=filled;")
                            ->writeln("color=yellow;")
                            ->writeln('sub2 -> sub3 -> sub4;')
                    )
                )
            ->render();
    }

}
