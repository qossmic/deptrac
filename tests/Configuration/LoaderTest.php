<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use Tests\Qossmic\Deptrac\CrossOsAgnosticEqualsTrait;
use function array_map;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Exception\File\CouldNotReadFileException;
use Symfony\Component\Filesystem\Path;

/**
 * @covers \Qossmic\Deptrac\Configuration\Loader
 */
final class LoaderTest extends TestCase
{
    private Loader $loader;

    protected function setUp(): void
    {
        $this->loader = new Loader(new Loader\YmlFileLoader(), __DIR__);
    }

    public function testLoadReturnsConfiguration(): void
    {
        $file = __DIR__.'/Fixtures/depfile.yaml';

        $configuration = $this->loader->load($file);

        self::assertSame(
            [Path::canonicalize(__DIR__.'/examples/Uncovered/')],
            array_map([Path::class, 'canonicalize'], $configuration->getPaths())
        );
        self::assertSame(
            [
                'currentWorkingDirectory' => __DIR__,
                'depfileDirectory' =>Path::normalize( __DIR__.'/Fixtures'),
            ],
            $configuration->getParameters()
        );
    }

    public function testLoadReturnsConfigurationWithRelativePathOption(): void
    {
        $file = __DIR__.'/Fixtures/depfile-relative.yaml';

        $configuration = $this->loader->load($file);

        self::assertSame(
            [Path::canonicalize(__DIR__.'/Fixtures/examples/Uncovered/')],
            array_map([Path::class, 'canonicalize'], $configuration->getPaths())
        );
    }

    public function testLoadThrowsImportFileCannotBeReadException(): void
    {
        $file = __DIR__.'/Fixtures/non-existent-baseline.yml';

        $this->expectException(CouldNotReadFileException::class);
        $this->expectExceptionMessageMatches('~non-existent-file\.yaml~');

        $this->loader->load($file);
    }

    public function testLoadWithBaseline(): void
    {
        $file = __DIR__.'/Fixtures/baseline-main.yml';

        $configuration = $this->loader->load($file);

        self::assertSame(
            [
                'DummyClass' => [
                    'FooClass',
                    'BarClass',
                ],
            ],
            $configuration->getRuleset()->getSkipViolations()
        );
    }

    public function testLoadWithImports(): void
    {
        $file = __DIR__.'/Fixtures/imports.yaml';

        $configuration = $this->loader->load($file);

        self::assertCount(1, $configuration->getLayers());
        self::assertSame(
            [Path::canonicalize(__DIR__.'/Fixtures/examples/Uncovered/')],
            array_map([Path::class, 'canonicalize'], $configuration->getPaths())
        );
    }
}
