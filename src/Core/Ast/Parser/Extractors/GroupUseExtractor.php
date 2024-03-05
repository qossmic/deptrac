<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<GroupUse>
 */
class GroupUseExtractor implements ReferenceExtractorInterface
{
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        assert($referenceBuilder instanceof FileReferenceBuilder);
        foreach ($node->uses as $use) {
            if (Use_::TYPE_NORMAL === $use->type) {
                $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                $referenceBuilder->useStatement($classLikeName, $use->name->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        assert($referenceBuilder instanceof FileReferenceBuilder);
        foreach ($node->uses as $use) {
            if (Use_::TYPE_NORMAL === $use->type) {
                $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                $referenceBuilder->useStatement($classLikeName, $use->name->getLine());
            }
        }
    }

    public function getNodeType(): string
    {
        return GroupUse::class;
    }
}
