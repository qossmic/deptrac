<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;

final class DependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new Dependency(
            ClassLikeName::fromFQCN('a'),
            ClassLikeName::fromFQCN('b'),
            FileOccurrence::fromFilepath('/foo.php', 23)
        );
        self::assertSame('a', $dependency->getClassLikeNameA()->toString());
        self::assertSame('/foo.php', $dependency->getFileOccurrence()->getFilepath());
        self::assertSame(23, $dependency->getFileOccurrence()->getLine());
        self::assertSame('b', $dependency->getClassLikeNameB()->toString());
    }
}
