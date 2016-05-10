<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graphviz;

class DotWriter
{

    private $graphType;

    private $indent = 0;

    private $buffer = '';

    /**
     * @param $graphType
     */
    private function __construct($graphType)
    {
        $this->graphType = $graphType;
    }

    public static function newDigraph() {
        return new static('digraph');
    }

    public static function newSubgraph() {
        return new static('subgraph');
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

        if ($this->graphType == 'digraph') {
            $out = $this->graphType . ' G  {'."\n";
        } else {
            $out = $this->graphType . ' cluster_'.uniqid().' {'."\n";
        }


        $out .= implode("\n", array_filter(array_map(function($v) use ($parent) {

            if (!$v) {
                return;
            }

            return str_repeat("\t", ($parent ? $parent->getIndent() : 0) + 1) . $v;
        }, explode("\n", $this->buffer))));

        $out .= "\n}\n";

        return $out;
    }



}
