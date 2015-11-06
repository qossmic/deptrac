<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class UseDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'UseDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {

        foreach ($astMap->getAsts() as $ast) {
            $uses = [];

            foreach ($ast as $namespaceNode) {
                if (!$namespaceNode instanceof Namespace_ || !$namespaceNode->stmts) {
                    continue;
                }

                foreach ($namespaceNode->stmts as $useNodes) {
                    if (!$useNodes instanceof Use_) {
                        continue;
                    }

                    foreach ($useNodes->uses as $useNode) {
                        $uses[] = $useNode->name->toString();
                    }
                }

            }


            foreach (AstHelper::findClassLikeNodes($ast) as $classLikeNodes) {
                foreach ($uses as $use) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $classLikeNodes->namespacedName->toString(), '?', $use, '?', '?'
                        )
                    );
                }
            }
        }
    }

}
