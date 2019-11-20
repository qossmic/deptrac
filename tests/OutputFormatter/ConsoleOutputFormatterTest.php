<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
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
                new RulesetViolation(
                    new InheritDependency(
                        'ClassA',
                        'ClassB',
                        new Dependency('OriginalA', 12, 'OriginalB'),
                        AstInherit::newExtends('ClassInheritA', 3)->withPath([
                            AstInherit::newExtends('ClassInheritB', 4),
                            AstInherit::newExtends('ClassInheritC', 5),
                            AstInherit::newExtends('ClassInheritD', 6),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            '
                ClassA must not depend on ClassB (LayerA on LayerB)
                ClassInheritD::6 ->
                ClassInheritC::5 ->
                ClassInheritB::4 ->
                ClassInheritA::3 ->
                OriginalB::12

                Found 1 Violations
            ',
        ];

        yield [
            [
                new RulesetViolation(
                    new Dependency('OriginalA', 12, 'OriginalB'),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            '
                OriginalA::12 must not depend on OriginalB (LayerA on LayerB)

                Found 1 Violations
            ',
        ];

        yield [
            [],
            [],
            '

                Found 0 Violations
            ',
        ];

        yield [
            [
                $violation = new RulesetViolation(
                    new Dependency('OriginalA', 12, 'OriginalB'),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [
                $violation,
            ],
            '[SKIPPED] OriginalA::12 must not depend on OriginalB (LayerA on LayerB)
            Found 0 Violations and 1 Violations skipped
            ',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $violations, array $skippedViolations, string $expectedOutput): void
    {
        $output = new BufferedOutput();

        $formatter = new ConsoleOutputFormatter();
        $formatter->finish(
            new DependencyContext(
                $this->prophesize(AstMap::class)->reveal(),
                $this->prophesize(Result::class)->reveal(),
                $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
                $violations,
                $skippedViolations
            ),
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
