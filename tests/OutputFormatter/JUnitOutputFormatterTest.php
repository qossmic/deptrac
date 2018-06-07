<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\AstInherit;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\JUnitOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\BufferedOutput;

class JUnitOutputFormatterTest extends TestCase
{
    public function testGetName()
    {
        $this->assertEquals('junit', (new JUnitOutputFormatter())->getName());
    }

    public function basicDataProvider()
    {
        yield [
            [
                'LayerA',
                'LayerB'
            ],
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
                JUnitReportdump:<?xmlversion="1.0"encoding="UTF-8"?><testsuites><testsuiteid="1"package=""name="LayerA"hostname="localhost"tests="1"failures="1"errors="0"time="0"><testcasename="LayerA-ClassA"classname="ClassA"time="0"><failuremessage="ClassA:0mustnotdependonClassB(LayerAonLayerB)"type="WARNING"/></testcase></testsuite></testsuites>
            ',
        ];

        yield [
            [
                'LayerA',
                'LayerB'
            ],
            [
                new RulesetViolation(
                    new Dependency('OriginalA', 12, 'OriginalB'),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'JUnitReportdump:<?xmlversion="1.0"encoding="UTF-8"?><testsuites><testsuiteid="1"package=""name="LayerA"hostname="localhost"tests="1"failures="1"errors="0"time="0"><testcasename="LayerA-OriginalA"classname="OriginalA"time="0"><failuremessage="OriginalA:12mustnotdependonOriginalB(LayerAonLayerB)"type="WARNING"/></testcase></testsuite></testsuites>',
        ];

        yield [
            [

            ],
            [

            ],
            'JUnitReportdump:<?xmlversion="1.0"encoding="UTF-8"?><testsuites/>',
        ];
    }

    /**
     * @param array $layers
     * @param array $violations
     * @param       $expectedOutput
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $layers, array $violations, $expectedOutput)
    {
        $classNameResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        $classNameResolver->getLayers()->willReturn($layers);

        $output = new BufferedOutput();

        $formatter = new JUnitOutputFormatter();
        $formatter->finish(
            new DependencyContext(
                $this->prophesize(AstMap::class)->reveal(),
                $violations,
                $this->prophesize(Result::class)->reveal(),
                $classNameResolver->reveal()
            ),
            $output,
            new OutputFormatterInput(['dump-xml' => ''])
        );

        $o = $output->fetch();
        $this->assertEquals(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testGetOptions()
    {
        $this->assertCount(1, (new JUnitOutputFormatter())->configureOptions());
    }

    private function normalize($str)
    {
        return str_replace(["\t", "\n", ' '], '', $str);
    }
}
