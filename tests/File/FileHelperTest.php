<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\File;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\File\FileHelper;

class FileHelperTest extends TestCase
{
    /**
     * @dataProvider providePaths
     */
    public function testToAbsolutePath(string $path, string $workingDirectory, string $expectedPath): void
    {
        self::assertEquals($expectedPath, (new FileHelper($workingDirectory))->toAbsolutePath($path));
    }

    public function providePaths(): iterable
    {
        yield ['/tmp', '/path', '/tmp'];
        yield ['foo', '/path', '/path/foo'];
        yield ['./foo', '/path', '/path/foo'];
        yield ['C:\\foo', '/path', 'C:\\foo'];
        yield ['d:\\foo', '/path', 'd:\\foo'];
        yield ['E:/foo', '/path', 'E:/foo'];
        yield ['f:/foo', '/path', 'f:/foo'];
        yield ['://foo', '/path', '://foo'];
    }
}
