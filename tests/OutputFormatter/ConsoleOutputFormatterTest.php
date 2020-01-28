<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertEquals('console', (new ConsoleOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        yield [
            [
                new Violation(
                    new InheritDependency(
                        'ClassA',
                        'ClassB',
                        new Dependency('OriginalA', 'OriginalB', new FileOccurrence(new AstFileReference('originalA.php'), 12)),
                        AstInherit::newExtends('ClassInheritA', new FileOccurrence(new AstFileReference('originalA.php'), 3))
                            ->withPath([
                                AstInherit::newExtends('ClassInheritB', new FileOccurrence(new AstFileReference('originalA.php'), 4)),
                                AstInherit::newExtends('ClassInheritC', new FileOccurrence(new AstFileReference('originalA.php'), 5)),
                                AstInherit::newExtends('ClassInheritD', new FileOccurrence(new AstFileReference('originalA.php'), 6)),
                            ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            '
                ClassA must not depend on ClassB (LayerA on LayerB)
                ClassInheritD::6 ->
                ClassInheritC::5 ->
                ClassInheritB::4 ->
                ClassInheritA::3 ->
                OriginalB::12

                Report:
                Violations: 1
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
            ',
        ];

        yield [
            [
                new Violation(
                    new Dependency('OriginalA', 'OriginalB', new FileOccurrence(new AstFileReference('originalA.php'), 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            '
                OriginalA::12 must not depend on OriginalB (LayerA on LayerB)

                Report:
                Violations: 1
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
            ',
        ];

        yield [
            [],
            '

                Report:
                Violations: 0
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
            ',
        ];

        yield [
            [
                new SkippedViolation(
                    new Dependency('OriginalA', 'OriginalB', new FileOccurrence(new AstFileReference('originalA.php'), 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            '[SKIPPED] OriginalA::12 must not depend on OriginalB (LayerA on LayerB)
            
            Report:
            Violations: 0
            Skipped violations: 1
            Uncovered: 0
            Allowed: 0
            ',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, string $expectedOutput): void
    {
        $output = new BufferedOutput();

        $formatter = new ConsoleOutputFormatter();
        $formatter->finish(
            new Context($rules),
            $output,
            new OutputFormatterInput([])
        );

        $o = $output->fetch();
        static::assertEquals(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testGetOptions(): void
    {
        static::assertCount(0, (new ConsoleOutputFormatter())->configureOptions());
    }

    private function normalize($str)
    {
        return str_replace(["\r", "\t", "\n", ' '], '', $str);
    }
}
