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
        static::assertSame('a', $dependency->getClassLikeNameA()->toString());
        static::assertSame('/foo.php', $dependency->getFileOccurrence()->getFilepath());
        static::assertSame(23, $dependency->getFileOccurrence()->getLine());
        static::assertSame('b', $dependency->getClassLikeNameB()->toString());
    }
}
