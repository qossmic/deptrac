<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\DependencyInjection\ServiceContainerBuilder;

final class ServiceContainerBuilderTest extends TestCase
{
    public function testBuildsContainerWithDefaultParameters(): void
    {
        $builder = new ServiceContainerBuilder(__DIR__);

        $container = $builder->build();

        self::assertTrue($container->getParameter('ignore_uncovered_internal_classes'));
        self::assertSame(
            ['types' => ['class', 'use']],
            $container->getParameter('analyser')
        );
        self::assertSame(
            [],
            $container->getParameter('paths')
        );
        self::assertSame(
            [],
            $container->getParameter('exclude_files')
        );
        self::assertSame(
            [],
            $container->getParameter('layers')
        );
        self::assertSame(
            [],
            $container->getParameter('ruleset')
        );
        self::assertSame(
            [],
            $container->getParameter('skip_violations')
        );
    }
}
