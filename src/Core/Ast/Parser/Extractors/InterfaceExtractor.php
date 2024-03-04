<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

class InterfaceExtractor implements ReferenceExtractorInterface
{

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node instanceof Interface_) {
            assert($referenceBuilder instanceof ClassLikeReferenceBuilder);
            foreach ($node->extends as $extend) {
                $referenceBuilder->implements($extend->toCodeString(), $extend->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node instanceof Interface_) {
            assert($referenceBuilder instanceof ClassLikeReferenceBuilder);
            foreach ($node->extends as $extend) {
                $referenceBuilder->implements($extend->toCodeString(), $extend->getLine());
            }
        }
    }
}
