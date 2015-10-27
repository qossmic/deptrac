<?php

namespace DependencyTracker\Collector;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\ClassLayerMap;
use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\DependencyResult;
use PhpParser\Node\Stmt\Class_;

class ClassNameCollector implements CollectorInterface
{
    protected $layerConfiguration;

    protected $regex;

    public function getType()
    {
        return 'className';
    }

    public function __construct(
        ConfigurationLayer $layer,
        array $args
    )
    {
        $this->layerConfiguration = $layer;
        if (!isset($args['regex'])) {
            throw new \LogicException('ClassNameCollector needs the prefix attr.');
        }
        $this->regex = $args['regex'];
    }


    public function applyAstFile(AstMap $astMap, DependencyResult $dependencyResult)
    {
        foreach($astMap->getAsts() as $filePathName => $ast) {

            /** @var $classes Class_[] */
            $classes = AstHelper::findClassLikeNodes($ast);

            foreach ($classes as $klass) {

                if (!preg_match('/'.$this->regex.'/i', $klass->namespacedName->toString())) {
                    continue;
                }

                $dependencyResult->addClassToLayer($klass->namespacedName->toString(), $this->layerConfiguration->getName());
            }
        }
    }

}
