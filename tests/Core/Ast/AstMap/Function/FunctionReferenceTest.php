<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\AstMap\Function;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\TaggedTokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionReference;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionToken;
use Tests\Qossmic\Deptrac\Core\Ast\AstMap\TaggedTokenReferenceTestTrait;

final class FunctionReferenceTest extends TestCase
{
    use TaggedTokenReferenceTestTrait;

    private function newWithTags(array $tags): TaggedTokenReferenceInterface
    {
        return new FunctionReference(
            FunctionToken::fromFQCN('testing'),
            [],
            $tags
        );
    }
}
