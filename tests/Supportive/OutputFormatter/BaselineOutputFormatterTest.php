<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Ast\DependencyContext;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\BaselineOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data\DummyViolationCreatingRule;

class BaselineOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertSame('baseline', (new BaselineOutputFormatter())->getName());
    }

    public static function basicDataProvider(): iterable
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('originalA.php', 3),
                            AstInheritType::EXTENDS
                        ))
                            ->replacePath([
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('originalA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('originalA.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('originalA.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ])
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_1.yml'),
        ];

        yield [
            [
                new Violation(
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            file_get_contents(__DIR__.'/data/expected-baseline-report_3.yml'),
        ];

        yield [
            [
                new Uncovered(
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
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

        $analysisResult = new AnalysisResult();
        foreach ($rules as $rule) {
            $analysisResult->addRule($rule);
        }

        try {
            $output = new BufferedOutput();

            $formatter = new BaselineOutputFormatter();
            $formatter->finish(
                OutputResult::fromAnalysisResult($analysisResult),
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
