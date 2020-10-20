<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritanceFlatter;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;

final class InheritanceFlatterTest extends TestCase
{
    private function getAstClassReference($className)
    {
        $astClass = $this->prophesize(AstClassReference::class);
        $astClass->getClassLikeName()->willReturn(ClassLikeName::fromFQCN($className));

        return $astClass->reveal();
    }

    private function getDependency($className)
    {
        $dep = $this->prophesize(Dependency::class);
        $dep->getClassLikeNameA()->willReturn(ClassLikeName::fromFQCN($className));
        $dep->getClassLikeNameB()->willReturn(ClassLikeName::fromFQCN($className.'_b'));

        return $dep->reveal();
    }

    public function testFlattenDependencies(): void
    {
        $astMap = $this->prophesize(AstMap::class);

        $astMap->getAstClassReferences()->willReturn([
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

        $astMap->getClassInherits(ClassLikeName::fromFQCN('classA'))->willReturn([]);
        $astMap->getClassInherits(ClassLikeName::fromFQCN('classB'))->willReturn([]);
        $astMap->getClassInherits(ClassLikeName::fromFQCN('classBaum'))->willReturn([]);
        $astMap->getClassInherits(ClassLikeName::fromFQCN('classWeihnachtsbaum'))->willReturn([
            AstInherit::newTraitUse(ClassLikeName::fromFQCN('classBaum'), FileOccurrence::fromFilepath('classWeihnachtsbaum.php', 3)),
        ]);
        $astMap->getClassInherits(ClassLikeName::fromFQCN('classGeschmückterWeihnachtsbaum'))->willReturn([
            AstMap\AstInherit::newExtends(ClassLikeName::fromFQCN('classBaum'), FileOccurrence::fromFilepath('classGeschmückterWeihnachtsbaum.php', 3))
                ->withPath([
                    AstInherit::newTraitUse(ClassLikeName::fromFQCN('classWeihnachtsbaum'), FileOccurrence::fromFilepath('classBaum.php', 3)),
                ]),
        ]);

        (new InheritanceFlatter())->flattenDependencies($astMap->reveal(), $dependencyResult);

        $inheritDeps = array_filter(
            $dependencyResult->getDependenciesAndInheritDependencies(),
            static function ($v) {
                return $v instanceof InheritDependency;
            }
        );

        static::assertCount(2, $inheritDeps);
    }
}
