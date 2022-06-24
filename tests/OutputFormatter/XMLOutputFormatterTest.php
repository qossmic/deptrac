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
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\OutputFormatter\XMLOutputFormatter;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class XMLOutputFormatterTest extends TestCase
{
    private static $actual_xml_report_file = 'actual-deptrac-report.xml';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.self::$actual_xml_report_file)) {
            unlink(__DIR__.'/data/'.self::$actual_xml_report_file);
        }
    }

    public function testGetName(): void
    {
        self::assertSame('xml', (new XMLOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'), FileOccurrence::fromFilepath('ClassA.php', 12)),
                        AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('ClassA.php', 3))->withPath([
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('ClassInheritA.php', 4)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('ClassInheritB.php', 5)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('ClassInheritC.php', 6)),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-xml-report_1.xml',
        ];

        yield [
            [
                new Violation(
                    new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'), FileOccurrence::fromFilepath('ClassA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-xml-report_2.xml',
        ];

        yield [
            [],
            'expected-xml-report_3.xml',
        ];

        yield [
            [
                $violations = new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'), FileOccurrence::fromFilepath('ClassA.php', 12)),
                        AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('ClassA.php', 3))->withPath([
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('ClassInheritA.php', 4)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('ClassInheritB.php', 5)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('ClassInheritC.php', 6)),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'), FileOccurrence::fromFilepath('ClassA.php', 12)),
                        AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('ClassA.php', 3))->withPath([
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('ClassInheritA.php', 4)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('ClassInheritB.php', 5)),
                            AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('ClassInheritC.php', 6)),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-xml-report-with-skipped-violations.xml',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, $expectedOutputFile): void
    {
        $bufferedOutput = new BufferedOutput();

        $formatter = new XMLOutputFormatter();
        $formatter->finish(
            new LegacyResult($rules, [], []),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(__DIR__.'/data/'.self::$actual_xml_report_file, false, false, false)
        );

        self::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.self::$actual_xml_report_file,
            __DIR__.'/data/'.$expectedOutputFile
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
