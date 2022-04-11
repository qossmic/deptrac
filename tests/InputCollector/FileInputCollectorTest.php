<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\InputCollector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\InputCollector\FileInputCollector;
use Symfony\Component\Filesystem\Path;
use function array_values;
use function natcasesort;
use function sys_get_temp_dir;

final class FileInputCollectorTest extends TestCase
{
    public function testCollectsPhpFilesUsingAbsolutePath(): void
    {
        $collector = new FileInputCollector([__DIR__.'/Fixtures'], [], sys_get_temp_dir());

        $files = $collector->collect();

        natcasesort($files);

        self::assertSame(
            [Path::normalize(__DIR__.'/Fixtures/example.php')],
            array_values($files)
        );
    }

    public function testCollectsPhpFilesUsingRelativePath(): void
    {
        $collector = new FileInputCollector(['Fixtures'], [], __DIR__);

        $files = $collector->collect();

        natcasesort($files);

        self::assertSame(
            [Path::normalize(__DIR__.'/Fixtures/example.php')],
            array_values($files)
        );
    }
}
