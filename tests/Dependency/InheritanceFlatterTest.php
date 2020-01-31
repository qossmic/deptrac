<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritanceFlatter;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;

class InheritanceFlatterTest extends TestCase
{
    private function getAstReference($className)
    {
        $astClass = $this->prophesize(AstClassReference::class);
        $astClass->getClassName()->willReturn(ClassLikeName::fromString($className));

        return $astClass->reveal();
    }

    private function getDependency($className)
    {
        $dep = $this->prophesize(Dependency::class);
        $dep->getClassA()->willReturn(ClassLikeName::fromString($className));
        $dep->getClassB()->willReturn(ClassLikeName::fromString($className.'_b'));

        return $dep->reveal();
    }

    public function testFlattenDependencies(): void
    {
        $astMap = $this->prophesize(AstMap::class);

        $astMap->getAstClassReferences()->willReturn([
            $this->getAstReference('classA'),
            $this->getAstReference('classB'),
            $this->getAstReference('classBaum'),
            $this->getAstReference('classWeihnachtsbaum'),
            $this->getAstReference('classGeschmückterWeihnachtsbaum'),
        ]);

        $dependencyResult = new Result();
        $dependencyResult->addDependency($classADep = $this->getDependency('classA'));
        $dependencyResult->addDependency($classBDep = $this->getDependency('classB'));
        $dependencyResult->addDependency($classBaumDep = $this->getDependency('classBaum'));
        $dependencyResult->addDependency($classWeihnachtsbaumsDep = $this->getDependency('classWeihnachtsbaumsA'));

        $astMap->getClassInherits('classA')->willReturn([]);
        $astMap->getClassInherits('classB')->willReturn([]);
        $astMap->getClassInherits('classBaum')->willReturn([]);
        $astMap->getClassInherits('classWeihnachtsbaum')->willReturn([
            AstInherit::newTraitUse(ClassLikeName::fromString('classBaum'), new FileOccurrence(new AstFileReference('classWeihnachtsbaum.php'), 3)),
        ]);
        $astMap->getClassInherits('classGeschmückterWeihnachtsbaum')->willReturn([
            AstMap\AstInherit::newExtends(ClassLikeName::fromString('classBaum'), new FileOccurrence(new AstFileReference('classGeschmückterWeihnachtsbaum.php'), 3))
                ->withPath([
                    AstInherit::newTraitUse(ClassLikeName::fromString('classWeihnachtsbaum'), new FileOccurrence(new AstFileReference('classBaum.php'), 3)),
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
