<?php

namespace DependencyTracker\Collector;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\DependencyResult;
use PhpParser\Node\Stmt\ClassLike;

class ClassNameCollector implements CollectorInterface
{
    protected $layerConfiguration;

    protected $regex;

    public function getType()
    {
        return 'className';
    }

    private function getRegexByConfiguration(array $configuration)
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('ClassNameCollector needs the regex configuration.');
        }

        return $configuration['regex'];
    }

    public function applyAstFile(
        AstMap $astMap,
        DependencyResult $dependencyResult,
        ConfigurationLayer $layer,
        array $configuration
    )
    {

        $regex = $this->getRegexByConfiguration($configuration);

        foreach($astMap->getAsts() as $filePathName => $ast) {

            /** @var $classes ClassLike[] */
            $classes = AstHelper::findClassLikeNodes($ast);

            foreach ($classes as $klass) {

                if (!preg_match('/'.$regex.'/i', $klass->namespacedName->toString())) {
                    continue;
                }

                $dependencyResult->addClassToLayer($klass->namespacedName->toString(), $layer->getName());
            }
        }
    }

}
