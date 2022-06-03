<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Configuration\OutputFormatterInput;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\BaselineOutputFormatter;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class BaselineOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertSame('baseline', (new BaselineOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                        AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('originalA.php', 3))
                            ->withPath([
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('originalA.php', 4)),
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('originalA.php', 5)),
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('originalA.php', 6)),
                            ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_1.yml'),
        ];

        yield [
            [
                new Violation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_2.yml'),
        ];

        yield [
            [],
            "deptrac:\n  skip_violations: {  }\n",
        ];

        yield [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_3.yml'),
        ];

        yield [
            [
                new Uncovered(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA'
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_4.yml'),
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, string $expectedOutput): void
    {
        $generatedBaselineFile = tempnam(sys_get_temp_dir(), 'deptrac_');

        try {
            $output = new BufferedOutput();

            $formatter = new BaselineOutputFormatter();
            $formatter->finish(
                new LegacyResult($rules, [], []),
                $this->createSymfonyOutput($output),
                new OutputFormatterInput($generatedBaselineFile, false, false, false)
            );

            static::assertSame(
                $expectedOutput,
                file_get_contents($generatedBaselineFile)
            );
        } finally {
            unlink($generatedBaselineFile);
        }
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
