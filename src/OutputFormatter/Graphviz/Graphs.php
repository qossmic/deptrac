<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graphviz;

class Graphs
{

    private $style = 'filled';
    private $color = "lightgrey";
    private $label = '';
    private $path = [];
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
                ->writeln("style=filled;")
                ->writeln("color=lightgrey;")
                ->writeln('a1 -> b3 -> b2 -> a3 ->a3 -> a0;')
                ->writeln('a3 -> sub3;')
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
