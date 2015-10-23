<?php

namespace DependencyTracker;

class GraphDrawer
{

    public function draw(CollectionMap $map)
    {
        $graph = new \Fhaculty\Graph\Graph();
        $vertices = [];

        foreach ($map->getDependencies() as $from => $t) {

            foreach ($t as $to) {
                if (!isset($vertices[$to])) {
                    $vertices[$to] = $graph->createVertex($to);
                }
            }

            if (!isset($vertices[$from])) {
                $vertices[$from] = $graph->createVertex($from);
            }
        }

        foreach ($map->getDependencies() as $from => $t) {
            foreach ($t as $to) {
                $vertices[$from]->createEdgeTo($vertices[$to]);
            }
        }

        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);
    }

}
