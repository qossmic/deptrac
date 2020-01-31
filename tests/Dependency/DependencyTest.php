<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;

class DependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new Dependency(ClassLikeName::fromString('a'), ClassLikeName::fromString('b'), new FileOccurrence(new AstFileReference('/foo.php'), 23));
        static::assertEquals('a', $dependency->getClassA());
        static::assertEquals('/foo.php', $dependency->getFileOccurrence()->getFilenpath());
        static::assertEquals(23, $dependency->getFileOccurrence()->getLine());
        static::assertEquals('b', $dependency->getClassB());
    }
}
