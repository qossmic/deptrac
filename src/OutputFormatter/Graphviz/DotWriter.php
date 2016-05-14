<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graphviz;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\OutputFormatter\Graph\GraphDependency;

class DotWriter
{

    private $graphType;

    private $indent = 0;

    private $buffer = '';

    /** @var ConfigurationLayer|null */
    private $layer;

    /**
     * @param $graphType
     */
    private function __construct($graphType, ConfigurationLayer $layer = null)
    {
        $this->graphType = $graphType;
        $this->layer = $layer;
    }

    public static function newDigraph() {
        return new static('digraph');
    }

    public static function newSubgraph(ConfigurationLayer $layer) {
        return new static('subgraph', $layer);
    }

    /**
     * @param ConfigurationLayer $layer
     * @return string
     */
    private function findArrowDestination(ConfigurationLayer $layer) {
        if (!$layer->getLayers()) {
            return $layer->getPathname();
        }

        foreach ($layer->getLayers() as $sublayer) {
            if ($sublayer->getLayers()) {
                return $sublayer->getLayers()[0]->getPathname();
            }
        }

        return $this->findArrowDestination($layer->getLayers()[0]);
    }

    private function resolveClusterConnection(ConfigurationLayer $layer) {
        if (!$layer->getLayers()) {
            return null;
        }

        return 'cluster_'.$layer->getId();
    }

    public function writeViolationArrow(GraphDependency $a) {

        $ltail = $this->resolveClusterConnection($a->getLayerA());
        $lhead = $this->resolveClusterConnection($a->getLayerB());

        $this
            ->writeln(
                '"' . $this->findArrowDestination($a->getLayerA()) .
                '" -> "' .
                $this->findArrowDestination($a->getLayerB()) .
                '" [label = "' . count($a->getDependencies()) . '" color=red '.($ltail?'ltail='.$ltail:'').' '.($lhead?'lhead='.$lhead:'').' ];'
            )
        ;
    }

    public function writeArrow(GraphDependency $a) {

        $ltail = $this->resolveClusterConnection($a->getLayerA());
        $lhead = $this->resolveClusterConnection($a->getLayerB());

        $this
            ->writeln(
                '"' . $this->findArrowDestination($a->getLayerA()) .
                '" -> "' .
                $this->findArrowDestination($a->getLayerB()) .
                '" [label = "' . count($a->getViolations()) . '" color=black '.($ltail?'ltail='.$ltail:'').' '.($lhead?'lhead='.$lhead:'').' ];'
            )
        ;
    }

    public function writeln($data) {

        if ($data instanceof DotWriter) {
            $this->buffer .= $data->render($this);
            return $this;
        }

        $this->buffer .= "\n".$data."\n";

        return $this;
    }

    /** @return int */
    public function getIndent()
    {
        return $this->indent;
    }

    public function render(DotWriter $parent = null)
    {

        $body = implode("\n", array_filter(array_map(function($v) use ($parent) {

            if (!$v) {
                return;
            }

            return str_repeat("\t", ($parent ? $parent->getIndent() : 0) + 1) . $v;
        }, explode("\n", $this->buffer))));

        if (!trim($body)) {
            return '';
        }

        if ($this->graphType == 'digraph') {
            $out = $this->graphType . ' G  {'."\n\tcompound=true;\n";
        } else {
            $out = $this->graphType . ' cluster_'.($this->layer ? $this->layer->getId() : uniqid()).' {'."\n";
        }

        $out .= $body;



        $out .= "\n}\n";

        return $out;
    }



}
