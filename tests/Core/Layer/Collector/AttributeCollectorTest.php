<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Layer\Collector\AttributeCollector;

final class AttributeCollectorTest extends TestCase
{
    private AttributeCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new AttributeCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield 'matches usage of attribute with only partial name' => [
            ['value' => 'MyAttribute'],
            true,
        ];
        yield 'does not match unescaped fully qualified class name' => [
            ['value' => 'App\MyAttribute'],
            true,
        ];
        yield 'does not match other attributes' => [
            ['value' => 'OtherAttribute'],
            false,
        ];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $config, bool $expected): void
    {
        $classLikeReference = FileReferenceBuilder::create('Foo.php')
            ->newClass('App\Foo', [], false)
            ->attribute('App\MyAttribute', 2)
            ->attribute('MyAttribute', 3)
            ->build();
        $actual = $this->collector->satisfy($config, $classLikeReference);

        self::assertSame($expected, $actual);
    }
}
