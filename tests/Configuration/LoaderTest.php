<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\Loader;

/**
 * @covers \Qossmic\Deptrac\Configuration\Loader
 */
final class LoaderTest extends TestCase
{
    public function testLoadReturnsConfiguration(): void
    {
        $file = __DIR__.'/Fixtures/Uncovered.depfile.yaml';

        $loader = new Loader(
            new Loader\YmlFileLoader()
        );

        $configuration = $loader->load($file);
        self::assertInstanceOf(Configuration::class, $configuration);
    }
}
