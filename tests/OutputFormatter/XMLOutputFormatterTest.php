<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\JUnitOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\XMLOutputFormatter;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\BufferedOutput;

class XMLOutputFormatterTest extends TestCase
{
    private static $actual_xml_report_file = 'actual-deptrac-report.xml';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.static::$actual_xml_report_file)) {
            unlink(__DIR__.'/data/'.static::$actual_xml_report_file);
        }
    }

    public function testGetName(): void
    {
        static::assertSame('xml', (new XMLOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
                        new Dependency(ClassLikeName::fromFQCN('OriginalA'), ClassLikeName::fromFQCN('OriginalB'), new FileOccurrence(new AstFileReference('ClassA.php'), 12)),
                        AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritA'), new FileOccurrence(new AstFileReference('ClassA.php'), 3))->withPath([
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritB'), new FileOccurrence(new AstFileReference('ClassInheritA.php'), 4)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritC'), new FileOccurrence(new AstFileReference('ClassInheritB.php'), 5)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritD'), new FileOccurrence(new AstFileReference('ClassInheritC.php'), 6)),
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
                    new Dependency(ClassLikeName::fromFQCN('OriginalA'), ClassLikeName::fromFQCN('OriginalB'), new FileOccurrence(new AstFileReference('ClassA.php'), 12)),
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
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
                        new Dependency(ClassLikeName::fromFQCN('OriginalA'), ClassLikeName::fromFQCN('OriginalB'), new FileOccurrence(new AstFileReference('ClassA.php'), 12)),
                        AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritA'), new FileOccurrence(new AstFileReference('ClassA.php'), 3))->withPath([
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritB'), new FileOccurrence(new AstFileReference('ClassInheritA.php'), 4)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritC'), new FileOccurrence(new AstFileReference('ClassInheritB.php'), 5)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritD'), new FileOccurrence(new AstFileReference('ClassInheritC.php'), 6)),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeName::fromFQCN('ClassC'),
                        ClassLikeName::fromFQCN('ClassD'),
                        new Dependency(ClassLikeName::fromFQCN('OriginalA'), ClassLikeName::fromFQCN('OriginalB'), new FileOccurrence(new AstFileReference('ClassA.php'), 12)),
                        AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritA'), new FileOccurrence(new AstFileReference('ClassA.php'), 3))->withPath([
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritB'), new FileOccurrence(new AstFileReference('ClassInheritA.php'), 4)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritC'), new FileOccurrence(new AstFileReference('ClassInheritB.php'), 5)),
                            AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritD'), new FileOccurrence(new AstFileReference('ClassInheritC.php'), 6)),
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
        $output = new BufferedOutput();

        $formatter = new XMLOutputFormatter();
        $formatter->finish(
            new Context($rules),
            $output,
            new OutputFormatterInput(['dump-xml' => __DIR__.'/data/'.static::$actual_xml_report_file])
        );

        static::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.static::$actual_xml_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    public function testGetOptions(): void
    {
        static::assertCount(1, (new JUnitOutputFormatter())->configureOptions());
    }
}
