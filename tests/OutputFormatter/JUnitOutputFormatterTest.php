<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\JUnitOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Result\Error;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Violation;
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
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('foo.php', 12)),
                        AstInherit::newExtends($classInheritA, FileOccurrence::fromFilepath('foo.php', 3))->withPath([
                            AstInherit::newExtends($classInheritB, FileOccurrence::fromFilepath('foo.php', 4)),
                            AstInherit::newExtends($classInheritC, FileOccurrence::fromFilepath('foo.php', 5)),
                            AstInherit::newExtends($classInheritD, FileOccurrence::fromFilepath('foo.php', 6)),
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
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('foo.php', 12)),
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
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('foo.php', 12)),
                        AstInherit::newExtends($classInheritA, FileOccurrence::fromFilepath('foo.php', 3))->withPath([
                            AstInherit::newExtends($classInheritB, FileOccurrence::fromFilepath('foo.php', 4)),
                            AstInherit::newExtends($classInheritC, FileOccurrence::fromFilepath('foo.php', 5)),
                            AstInherit::newExtends($classInheritD, FileOccurrence::fromFilepath('foo.php', 6)),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('foo.php', 12)),
                        AstInherit::newExtends($classInheritA, FileOccurrence::fromFilepath('foo.php', 3))->withPath([
                            AstInherit::newExtends($classInheritB, FileOccurrence::fromFilepath('foo.php', 4)),
                            AstInherit::newExtends($classInheritC, FileOccurrence::fromFilepath('foo.php', 5)),
                            AstInherit::newExtends($classInheritD, FileOccurrence::fromFilepath('foo.php', 6)),
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
        $formatter = new JUnitOutputFormatter();
        $formatter->finish(
            new LegacyResult($rules, [], []),
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
            new LegacyResult([], [
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
