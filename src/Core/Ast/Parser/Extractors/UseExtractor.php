<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<Use_>
 */
class UseExtractor implements ReferenceExtractorInterface
{
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (Use_::TYPE_NORMAL === $node->type) {
            assert($referenceBuilder instanceof FileReferenceBuilder);
            foreach ($node->uses as $use) {
                $referenceBuilder->useStatement($use->name->toString(), $use->name->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if (Use_::TYPE_NORMAL === $node->type) {
            assert($referenceBuilder instanceof FileReferenceBuilder);
            foreach ($node->uses as $use) {
                $referenceBuilder->useStatement($use->name->toString(), $use->name->getLine());
            }
        }
    }

    public function getNodeType(): string
    {
        return Use_::class;
    }
}
