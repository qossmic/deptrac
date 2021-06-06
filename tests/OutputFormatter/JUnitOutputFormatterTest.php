<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\JUnitOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;
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
        $originalA = ClassLikeName::fromFQCN('OriginalA');
        $originalB = ClassLikeName::fromFQCN('OriginalB');
        $classInheritA = ClassLikeName::fromFQCN('ClassInheritA');
        $classInheritB = ClassLikeName::fromFQCN('ClassInheritB');
        $classInheritC = ClassLikeName::fromFQCN('ClassInheritC');
        $classInheritD = ClassLikeName::fromFQCN('ClassInheritD');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
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
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
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
                        ClassLikeName::fromFQCN('ClassC'),
                        ClassLikeName::fromFQCN('ClassD'),
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
            new Context($rules, [], [], []),
            $this->createSymfonyOutput(new BufferedOutput()),
            new OutputFormatterInput([
                JUnitOutputFormatter::DUMP_XML => __DIR__.'/data/'.self::$actual_junit_report_file,
            ])
        );

        self::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.self::$actual_junit_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    public function testGetOptions(): void
    {
        self::assertCount(1, (new JUnitOutputFormatter())->configureOptions());
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
