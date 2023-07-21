<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Layer\Collector\ComposerCollector;

final class ComposerCollectorTest extends TestCase
{
    private ComposerCollector $sut;

    public function setUp(): void
    {
        $this->sut = new ComposerCollector();
    }

    public static function dataProviderSatisfy(): iterable
    {
        yield [
            [
                'composerPath' => __DIR__.DIRECTORY_SEPARATOR.'data/composer.json',
                'composerLockPath' => __DIR__.DIRECTORY_SEPARATOR.'data/composer.lock',
                'packages' => ['phpstan/phpdoc-parser'],
            ],
            'PHPStan\\PhpDocParser\\Ast\\Attribute',
            true,
        ];
        yield [
            [
                'composerPath' => __DIR__.DIRECTORY_SEPARATOR.'data/composer.json',
                'composerLockPath' => __DIR__.DIRECTORY_SEPARATOR.'data/composer.lock',
                'packages' => ['phpstan/phpdoc-parser'],
            ],
            'Completely\\Wrong\\Namespace\\Attribute',
            false,
        ];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = $this->sut->satisfy(
            $configuration,
            new ClassLikeReference(ClassLikeToken::fromFQCN($className), ClassLikeType::TYPE_CLASS),
        );

        self::assertSame($expected, $stat);
    }
}
