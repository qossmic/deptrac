<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use Exception;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\JsonOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class JsonOutputFormatterTest extends TestCase
{
    private static $actual_json_report_file = 'actual-deptrac-report.json';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.self::$actual_json_report_file)) {
            unlink(__DIR__.'/data/'.self::$actual_json_report_file);
        }
    }

    public function testGetName(): void
    {
        self::assertSame('json', (new JsonOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        yield 'Multiple violations' => [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassA.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassC.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerC'
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassE'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassC.php', 15)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerE'
                ),
            ],
            'expected-json-report_1.json',
        ];

        yield [
            [
                new Violation(
                    new Dependency(
                        ClassLikeToken::fromFQCN('OriginalA'),
                        ClassLikeToken::fromFQCN('OriginalB'),
                        FileOccurrence::fromFilepath('ClassA.php', 12)
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-json-report_2.json',
        ];

        yield [
            [],
            'expected-json-report_3.json',
        ];

        yield [
            [
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassA.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
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
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassC.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-json-report_4.json',
            true,
        ];

        yield 'Different violations types in one report' => [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassA.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
                                ),
                            ]
                        )
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(
                            ClassLikeToken::fromFQCN('OriginalA'),
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassA.php', 15)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
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
                            ClassLikeToken::fromFQCN('OriginalB'),
                            FileOccurrence::fromFilepath('ClassC.php', 12)
                        ),
                        AstInherit::newExtends(
                            ClassLikeToken::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('ClassA.php', 3)
                        )->withPath(
                            [
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    FileOccurrence::fromFilepath('ClassInheritA.php', 4)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    FileOccurrence::fromFilepath('ClassInheritB.php', 5)
                                ),
                                AstInherit::newExtends(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    FileOccurrence::fromFilepath('ClassInheritC.php', 6)
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
                        ClassLikeToken::fromFQCN('OriginalB'),
                        FileOccurrence::fromFilepath('OriginalA.php', 12)
                    ),
                    'LayerA'
                ),
            ],
            'expected-json-report_5.json',
            true,
            true,
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testFileOutput(
        array $rules,
        $expectedOutputFile,
        bool $reportSkipped = false,
        bool $reportUncovered = false
    ): void {
        $bufferedOutput = new BufferedOutput();

        $formatter = new JsonOutputFormatter();
        $formatter->finish(
            new LegacyResult($rules, [], []),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                __DIR__.'/data/'.self::$actual_json_report_file,
                $reportSkipped,
                $reportUncovered,
                false,
            )
        );

        self::assertJsonFileEqualsJsonFile(
            __DIR__.'/data/'.self::$actual_json_report_file,
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
        bool $reportUncovered = false
    ): void {
        $bufferedOutput = new BufferedOutput();

        $formatter = new JsonOutputFormatter();
        $formatter->finish(
            new LegacyResult($rules, [], []),
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
        $formatter = new JsonOutputFormatter();

        $malformedCharacters = "\xB1\x31";
        $violation = new Violation(
            new Dependency(
                ClassLikeToken::fromFQCN('OriginalA'),
                ClassLikeToken::fromFQCN('OriginalB'.$malformedCharacters),
                FileOccurrence::fromFilepath('ClassA.php', 12)
            ),
            'LayerA',
            'LayerB'
        );

        self::expectException(Exception::class);
        self::expectExceptionMessage('Unable to render json output. '
                                     .'Malformed UTF-8 characters, possibly incorrectly encoded');
        $formatter->finish(
            new LegacyResult([$violation], [], []),
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
