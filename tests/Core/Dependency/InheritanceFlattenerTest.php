<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;
use Qossmic\Deptrac\Core\Dependency\InheritanceFlattener;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;

final class InheritanceFlattenerTest extends TestCase
{
    private function getAstClassReference($className)
    {
        $astClass = $this->createMock(ClassLikeReference::class);
        $astClass->method('getToken')->willReturn(ClassLikeToken::fromFQCN($className));

        return $astClass;
    }

    private function getDependency($className)
    {
        $dep = $this->createMock(Dependency::class);
        $dep->method('getDepender')->willReturn(ClassLikeToken::fromFQCN($className));
        $dep->method('getDependent')->willReturn(ClassLikeToken::fromFQCN($className.'_b'));

        return $dep;
    }

    public function testFlattenDependencies(): void
    {
        $astMap = $this->createMock(AstMap::class);

        $astMap->method('getClassLikeReferences')->willReturn([
            $this->getAstClassReference('classA'),
            $this->getAstClassReference('classB'),
            $this->getAstClassReference('classBaum'),
            $this->getAstClassReference('classWeihnachtsbaum'),
            $this->getAstClassReference('classGeschmückterWeihnachtsbaum'),
        ]);

        $dependencyResult = new DependencyList();
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
                new AstInherit(
                    ClassLikeToken::fromFQCN('classBaum'), new FileOccurrence('classWeihnachtsbaum.php', 3),
                    AstInheritType::USES
                ),
            ],
            // classGeschmückterWeihnachtsbaum
            [
                (new AstInherit(
                    ClassLikeToken::fromFQCN('classBaum'), new FileOccurrence('classGeschmückterWeihnachtsbaum.php', 3),
                    AstInheritType::EXTENDS
                ))
                    ->withPath([
                        new AstInherit(
                            ClassLikeToken::fromFQCN('classWeihnachtsbaum'),
                            new FileOccurrence('classBaum.php', 3),
                            AstInheritType::USES
                        ),
                    ]),
            ]
        );

        (new InheritanceFlattener())->flattenDependencies($astMap, $dependencyResult);

        $inheritDeps = array_filter(
            $dependencyResult->getDependenciesAndInheritDependencies(),
            static function ($v) {
                return $v instanceof InheritDependency;
            }
        );

        self::assertCount(2, $inheritDeps);
    }
}
