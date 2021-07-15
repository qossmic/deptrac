<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;
use Qossmic\Deptrac\ClassLikeLayerResolverInterface;
use Qossmic\Deptrac\MemoizedClassLikeLayerResolver;

final class MemoizedClassLikeLayerResolverTest extends TestCase
{
    public function testGetLayersByClassLikeName(): void
    {
        $classLikeName = ClassLikeName::fromFQCN('foo');
        $decorated = $this->prophesize(ClassLikeLayerResolverInterface::class);
        $decorated->getLayersByClassLikeName($classLikeName)->willReturn(['bar']);

        $decorator = new MemoizedClassLikeLayerResolver($decorated->reveal());

        self::assertEquals(['bar'], $decorator->getLayersByClassLikeName($classLikeName));
        self::assertEquals(['bar'], $decorator->getLayersByClassLikeName($classLikeName));
    }
}
