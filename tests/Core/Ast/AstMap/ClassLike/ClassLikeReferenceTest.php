<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\TaggedTokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Tests\Qossmic\Deptrac\Core\Ast\AstMap\TaggedTokenReferenceTestTrait;

final class ClassLikeReferenceTest extends TestCase
{
    use TaggedTokenReferenceTestTrait;

    private function newWithTags(array $tags): TaggedTokenReferenceInterface
    {
        return new ClassLikeReference(
            ClassLikeToken::fromFQCN('Test'),
            ClassLikeType::TYPE_CLASS,
            [],
            [],
            $tags
        );
    }
}
