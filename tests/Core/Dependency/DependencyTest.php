<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Core\Dependency\Dependency;

final class DependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('a'),
            ClassLikeToken::fromFQCN('b'),
            FileOccurrence::fromFilepath('/foo.php', 23)
        );
        self::assertSame('a', $dependency->getDepender()->toString());
        self::assertSame('/foo.php', $dependency->getFileOccurrence()->filepath);
        self::assertSame(23, $dependency->getFileOccurrence()->line);
        self::assertSame('b', $dependency->getDependent()->toString());
    }
}
