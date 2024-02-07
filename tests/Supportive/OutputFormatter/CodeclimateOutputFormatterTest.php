<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use Exception;
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
use Qossmic\Deptrac\Supportive\OutputFormatter\CodeclimateOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data\DummyViolationCreatingRule;

final class CodeclimateOutputFormatterTest extends TestCase
{
    private static $actual_codeclimate_report_file = 'actual-deptrac-report.json';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.self::$actual_codeclimate_report_file)) {
            unlink(__DIR__.'/data/'.self::$actual_codeclimate_report_file);
        }
    }

    public function testGetName(): void
    {
        self::assertSame('codeclimate', CodeclimateOutputFormatter::getName());
    }

    public static function basicDataProvider(): iterable
    {
        yield 'Multiple violations' => [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath(
                            [
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('ClassInheritA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('ClassInheritB.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('ClassInheritC.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassC.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath(
                            [
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('ClassInheritA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('ClassInheritB.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('ClassInheritC.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerC',
                    new DummyViolationCreatingRule()
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassE'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassC.php', 15), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath(
                            [
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('ClassInheritA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('ClassInheritB.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('ClassInheritC.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerE',
                    new DummyViolationCreatingRule()
                ),
            ],
            'expected-codeclimate-report_1.json',
        ];

        yield [
            [
                new Violation(
                    new Dependency(
                        ClassLikeToken::fromFQCN('OriginalA'),
                        ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            'expected-codeclimate-report_2.json',
        ];

        yield [
            [],
            'expected-codeclimate-report_3.json',
        ];

        yield [
            [
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath(
                            [
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('ClassInheritA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('ClassInheritB.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('ClassInheritC.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassC.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath(
                            [
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('ClassInheritA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('ClassInheritB.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('ClassInheritC.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-codeclimate-report_4.json',
            true,
        ];

        $multipleViolationTypes = [
            new Violation(
                new InheritDependency(
                    ClassLikeToken::fromFQCN('ClassA'),
                    ClassLikeToken::fromFQCN('ClassB'),
                    new Dependency(
                        ClassLikeToken::fromFQCN('OriginalA'),
                        ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                    ),
                    (new AstInherit(
                        ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                        AstInheritType::EXTENDS
                    ))->replacePath(
                        [
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'), new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'), new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'), new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ]
                    )
                ),
                'LayerA',
                'LayerB',
                new DummyViolationCreatingRule()
            ),
            new SkippedViolation(
                new InheritDependency(
                    ClassLikeToken::fromFQCN('ClassA'),
                    ClassLikeToken::fromFQCN('ClassB'),
                    new Dependency(
                        ClassLikeToken::fromFQCN('OriginalA'),
                        ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassA.php', 15), DependencyType::PARAMETER)
                    ),
                    (new AstInherit(
                        ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                        AstInheritType::EXTENDS
                    ))->replacePath(
                        [
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'), new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'), new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'), new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ]
                    )
                ),
                'LayerA',
                'LayerB'
            ),
            new SkippedViolation(
                new InheritDependency(
                    ClassLikeToken::fromFQCN('ClassC'),
                    ClassLikeToken::fromFQCN('ClassD'),
                    new Dependency(
                        ClassLikeToken::fromFQCN('OriginalA'),
                        ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('ClassC.php', 12), DependencyType::PARAMETER)
                    ),
                    (new AstInherit(
                        ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                        AstInheritType::EXTENDS
                    ))->replacePath(
                        [
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'), new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'), new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'), new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ]
                    )
                ),
                'LayerA',
                'LayerB'
            ),
            new Uncovered(
                new Dependency(
                    ClassLikeToken::fromFQCN('OriginalA'),
                    ClassLikeToken::fromFQCN('OriginalB'), new DependencyContext(new FileOccurrence('OriginalA.php', 12), DependencyType::PARAMETER)
                ),
                'LayerA'
            ),
        ];

        yield 'Different violations types in one report' => [
            $multipleViolationTypes,
            'expected-codeclimate-report_5.json',
            true,
            true,
        ];

        yield 'Check custom severities for different violation types in one report' => [
            $multipleViolationTypes,
            'expected-codeclimate-report_6.json',
            true,
            true,
            [
                'severity' => [
                    'failure' => 'blocker',
                    'skipped' => 'critical',
                    'uncovered' => 'minor',
                ],
            ],
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testFileOutput(
        array $rules,
        $expectedOutputFile,
        bool $reportSkipped = false,
        bool $reportUncovered = false,
        array $inputConfig = []
    ): void {
        $bufferedOutput = new BufferedOutput();

        $analysisResult = new AnalysisResult();
        foreach ($rules as $rule) {
            $analysisResult->addRule($rule);
        }

        $formatter = new CodeclimateOutputFormatter(new FormatterConfiguration([
            'codeclimate' => $inputConfig,
        ]));
        $formatter->finish(
            OutputResult::fromAnalysisResult($analysisResult),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                __DIR__.'/data/'.self::$actual_codeclimate_report_file,
                $reportSkipped,
                $reportUncovered,
                false,
            )
        );

        self::assertJsonFileEqualsJsonFile(
            __DIR__.'/data/'.self::$actual_codeclimate_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testConsoleOutput(
        array $rules,
        $expectedOutputFile,
        bool $reportSkipped = false,
        bool $reportUncovered = false,
        array $inputConfig = []
    ): void {
        $bufferedOutput = new BufferedOutput();

        $formatter = new CodeclimateOutputFormatter(new FormatterConfiguration([
            'codeclimate' => $inputConfig,
        ]));

        $analysisResult = new AnalysisResult();
        foreach ($rules as $rule) {
            $analysisResult->addRule($rule);
        }

        $formatter->finish(
            OutputResult::fromAnalysisResult($analysisResult),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                null,
                $reportSkipped,
                $reportUncovered,
                false
            )
        );

        self::assertJsonStringEqualsJsonFile(
            __DIR__.'/data/'.$expectedOutputFile,
            $bufferedOutput->fetch()
        );
    }

    public function testJsonRenderError(): void
    {
        $bufferedOutput = new BufferedOutput();
        $formatter = new CodeclimateOutputFormatter(new FormatterConfiguration([]));

        $malformedCharacters = "\xB1\x31";
        $violation = new Violation(
            new Dependency(
                ClassLikeToken::fromFQCN('OriginalA'),
                ClassLikeToken::fromFQCN('OriginalB'.$malformedCharacters), new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
            ),
            'LayerA',
            'LayerB',
            new DummyViolationCreatingRule()
        );

        $analysisResult = new AnalysisResult();
        $analysisResult->addRule($violation);

        self::expectException(Exception::class);
        self::expectExceptionMessage('Unable to render codeclimate output. '
                                     .'Malformed UTF-8 characters, possibly incorrectly encoded');
        $formatter->finish(
            OutputResult::fromAnalysisResult($analysisResult),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                null,
                false,
                false,
                false,
            )
        );
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
