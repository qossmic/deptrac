<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResultBuilder;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\JUnitOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class JUnitOutputFormatterTest extends TestCase
{
    private static $actual_junit_report_file = 'actual-junit-report.xml';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.self::$actual_junit_report_file)) {
            unlink(__DIR__.'/data/'.self::$actual_junit_report_file);
        }
    }

    public function testGetName(): void
    {
        self::assertSame('junit', (new JUnitOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');
        $classInheritA = ClassLikeToken::fromFQCN('ClassInheritA');
        $classInheritB = ClassLikeToken::fromFQCN('ClassInheritB');
        $classInheritC = ClassLikeToken::fromFQCN('ClassInheritC');
        $classInheritD = ClassLikeToken::fromFQCN('ClassInheritD');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, new FileOccurrence('foo.php', 12), DependencyType::PARAMETER),
                        (new AstInherit(
                            $classInheritA, new FileOccurrence('foo.php', 3),
                            AstInheritType::EXTENDS
                        ))->withPath([
                            new AstInherit(
                                $classInheritB, new FileOccurrence('foo.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritC, new FileOccurrence('foo.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritD, new FileOccurrence('foo.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-junit-report_1.xml',
        ];

        yield [
            [
                new Violation(
                    new Dependency($originalA, $originalB, new FileOccurrence('foo.php', 12), DependencyType::PARAMETER),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-junit-report_2.xml',
        ];

        yield [
            [],
            'expected-junit-report_3.xml',
        ];

        yield [
            [
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, new FileOccurrence('foo.php', 12), DependencyType::PARAMETER),
                        (new AstInherit(
                            $classInheritA, new FileOccurrence('foo.php', 3),
                            AstInheritType::EXTENDS
                        ))->withPath([
                            new AstInherit(
                                $classInheritB, new FileOccurrence('foo.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritC, new FileOccurrence('foo.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritD, new FileOccurrence('foo.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency($originalA, $originalB, new FileOccurrence('foo.php', 12), DependencyType::PARAMETER),
                        (new AstInherit(
                            $classInheritA, new FileOccurrence('foo.php', 3),
                            AstInheritType::EXTENDS
                        ))->withPath([
                            new AstInherit(
                                $classInheritB, new FileOccurrence('foo.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritC, new FileOccurrence('foo.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                $classInheritD, new FileOccurrence('foo.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-junit-report-with-skipped-violations.xml',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, string $expectedOutputFile): void
    {
        $resultBuilder = new AnalysisResultBuilder();
        foreach ($rules as $rule) {
            $resultBuilder->add($rule);
        }

        $formatter = new JUnitOutputFormatter();
        $formatter->finish(
            $resultBuilder->build(),
            $this->createSymfonyOutput(new BufferedOutput()),
            new OutputFormatterInput(__DIR__.'/data/'.self::$actual_junit_report_file,
                false, false, false)
        );

        self::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.self::$actual_junit_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    public function testUnmatchedSkipped(): void
    {
        $formatter = new JUnitOutputFormatter();
        $formatter->finish(
            new OutputResult([], [
                new Error('Skipped violation "Class1" for "Class2" was not matched.'),
            ], []),
            $this->createSymfonyOutput(new BufferedOutput()),
            new OutputFormatterInput(__DIR__.'/data/'.self::$actual_junit_report_file,
                false, false, false)
        );

        self::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.self::$actual_junit_report_file,
            __DIR__.'/data/expected-junit-report-with-unmatched-violations.xml'
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
