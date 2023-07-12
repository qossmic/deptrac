<?php

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\MetaData\PackageName;
use Qossmic\Deptrac\Core\Layer\Collector\PackageNameCollector;

class PackageNameCollectorTest extends TestCase
{
    private PackageNameCollector $collector;

    public function setUp(): void
    {
        parent::setUp();

        $this->collector = new PackageNameCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield 'matches package name with only partial name' => [
            ['value' => 'Package'],
            ['MyPackage'],
            true,
        ];
        yield 'does not match partial name with full-string regex' => [
            ['value' => '^Package$'],
            ['MyPackage'],
            false,
        ];
        yield 'does not match missing package name' => [
            ['value' => 'Package'],
            [],
            false,
        ];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $config, array $packageNames, bool $expected): void
    {
        $metaData = array_map(static function ($packageName) {
            return new PackageName($packageName);
        }, $packageNames);

        $reference = $this->createMock(TokenReferenceInterface::class);
        $reference->method('getMetaData')
            ->willReturn($metaData);

        $this->assertSame($expected, $this->collector->satisfy($config, $reference));
    }
}
