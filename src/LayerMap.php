<?php 

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationView;

class LayerMap
{
    protected $layerDependencies = [];

    protected $viewConfiguration;

    /**
     * LayerMap constructor.
     * @param $viewConfiguration
     */
    public function __construct(ConfigurationView $viewConfiguration)
    {
        $this->viewConfiguration = $viewConfiguration;
    }

    public function addLayerDependency($layerA, $layerB, $classA, $classALine, $classB)
    {
        if(!isset($this->layerDependencies[$layerA.'|'.$layerB])) {
            $this->layerDependencies[$layerA.'|'.$layerB] = [
                'layerA' => $layerA,
                'layerB' => $layerB,
                'deps' => []
            ];
        }

        $this->layerDependencies[$layerA.'|'.$layerB]['deps'][] = [
            'classA' => $classA,
            'classALine' => $classALine,
            'classB' => $classB
        ];
    }

    public function getLayerDependencies()
    {
        return $this->layerDependencies;
    }

}
