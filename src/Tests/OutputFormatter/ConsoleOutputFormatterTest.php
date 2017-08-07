<?php

namespace SensioLabs\Deptrac\Tests\OutputFormatter;

use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\AstInherit;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleOutputFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertEquals('console', (new ConsoleOutputFormatter())->getName());
    }

    public function basicDataProvider()
    {
        yield [
            [
                new RulesetViolation(
                    new InheritDependency(
                        'ClassA',
                        'ClassB',
                        new Dependency('OriginalA', 12, 'OriginalB'),
                        new FlattenAstInherit(
                            AstInherit::newExtends('ClassInheritA', 3), [
                                AstInherit::newExtends('ClassInheritB', 4),
                                AstInherit::newExtends('ClassInheritC', 5),
                                AstInherit::newExtends('ClassInheritD', 6),
                            ]
                        )
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
            '
                OriginalA::12 must not depend [use] on OriginalB (LayerA on LayerB)

                Found 1 Violations
            ',
        ];

        yield [
            [

            ],
            '

                Found 0 Violations
            ',
        ];
    }

    /**
     * @param array $violations
     * @param $expectedOutput
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $violations, $expectedOutput)
    {
        $output = new BufferedOutput();

        $formatter = new ConsoleOutputFormatter();
        $formatter->finish(
            new DependencyContext(
                $this->prophesize(AstMap::class)->reveal(),
                $violations,
                $this->prophesize(DependencyResult::class)->reveal(),
                $this->prophesize(ClassNameLayerResolverInterface::class)->reveal()
            ),
            $output,
            new OutputFormatterInput([])
        );

        $o = $output->fetch();
        $this->assertEquals(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testGetOptions()
    {
        $this->assertCount(0, (new ConsoleOutputFormatter())->configureOptions());
    }

    private function normalize($str)
    {
        return str_replace(["\t", "\n", ' '], '', $str);
    }
}
