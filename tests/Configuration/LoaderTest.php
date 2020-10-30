<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Configuration\Loader;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Loader
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
