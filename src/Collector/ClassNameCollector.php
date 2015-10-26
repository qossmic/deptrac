<?php

namespace DependencyTracker\Collector;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\ClassLayerMap;
use DependencyTracker\Configuration\ConfigurationLayer;
use PhpParser\Node\Stmt\Class_;

class ClassNameCollector implements CollectorInterface
{
    protected $layerConfiguration;

    protected $fileLayerMap;

    protected $regex;

    public function getType()
    {
        return 'className';
    }

    public function __construct(
        ConfigurationLayer $layer,
        ClassLayerMap $layerMap,
        array $args
    )
    {
        $this->layerConfiguration = $layer;
        $this->layerMap = $layerMap;
        if (!isset($args['regex'])) {
            throw new \LogicException('ClassNameCollector needs the prefix attr.');
        }
        $this->regex = $args['regex'];
    }

    public function applyAstFile(AstMap $astMap)
    {
        foreach($astMap->getAsts() as $filePathName => $ast) {

            /** @var $classes Class_[] */
            $classes = AstHelper::findAstNodesOfType($ast, [Class_::class]);

            foreach ($classes as $klass) {

                if (!preg_match('/'.$this->regex.'/i', $klass->namespacedName->toString())) {
                    continue;
                }

                $this->layerMap->addClassToLayer($klass->namespacedName->toString(), $this->layerConfiguration->getName());
            }
        }
    }

}
