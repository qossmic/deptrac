<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritanceFlatter;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\Dependency\Result;

final class InheritanceFlatterTest extends TestCase
{
    private function getAstClassReference($className)
    {
        $astClass = $this->createMock(AstClassReference::class);
        $astClass->method('getTokenName')->willReturn(ClassLikeName::fromFQCN($className));

        return $astClass;
    }

    private function getDependency($className)
    {
        $dep = $this->createMock(Dependency::class);
        $dep->method('getDependant')->willReturn(ClassLikeName::fromFQCN($className));
        $dep->method('getDependee')->willReturn(ClassLikeName::fromFQCN($className.'_b'));

        return $dep;
    }

    public function testFlattenDependencies(): void
    {
        $astMap = $this->createMock(AstMap::class);

        $astMap->method('getAstClassReferences')->willReturn([
            $this->getAstClassReference('classA'),
            $this->getAstClassReference('classB'),
            $this->getAstClassReference('classBaum'),
            $this->getAstClassReference('classWeihnachtsbaum'),
            $this->getAstClassReference('classGeschmückterWeihnachtsbaum'),
        ]);

        $dependencyResult = new Result();
        $dependencyResult->addDependency($this->getDependency('classA'));
        $dependencyResult->addDependency($this->getDependency('classB'));
        $dependencyResult->addDependency($this->getDependency('classBaum'));
        $dependencyResult->addDependency($this->getDependency('classWeihnachtsbaumsA'));

        $astMap->method('getClassInherits')->willReturnOnConsecutiveCalls(
            // classA
            [],
            // classB
            [],
            // classBaum,
            [],
            // classWeihnachtsbaum
            [
                AstInherit::newTraitUse(ClassLikeName::fromFQCN('classBaum'), FileOccurrence::fromFilepath('classWeihnachtsbaum.php', 3)),
            ],
            // classGeschmückterWeihnachtsbaum
            [
                AstMap\AstInherit::newExtends(ClassLikeName::fromFQCN('classBaum'), FileOccurrence::fromFilepath('classGeschmückterWeihnachtsbaum.php', 3))
                    ->withPath([
                        AstInherit::newTraitUse(ClassLikeName::fromFQCN('classWeihnachtsbaum'), FileOccurrence::fromFilepath('classBaum.php', 3)),
                    ]),
            ]
        );

        (new InheritanceFlatter())->flattenDependencies($astMap, $dependencyResult);

        $inheritDeps = array_filter(
            $dependencyResult->getDependenciesAndInheritDependencies(),
            static function ($v) {
                return $v instanceof InheritDependency;
            }
        );

        self::assertCount(2, $inheritDeps);
    }
}
