<?php

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\Allowed;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\Supportive\OutputFormatter\MermaidJSOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data\DummyViolationCreatingRule;

class MermaidJSOutputFormatterTest extends TestCase
{
    /**
     * @dataProvider dataForTestFinish
     */
    public function testFinish(string $expected): void
    {
        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('ClassA'),
            ClassLikeToken::fromFQCN('ClassC'), new FileOccurrence('classA.php', 0), DependencyType::PARAMETER
        );

        $analysisResult = new AnalysisResult();
        $analysisResult->addRule(new Allowed($dependency, 'LayerA', 'LayerB'));
        $analysisResult->addRule(new Allowed($dependency, 'LayerC', 'LayerD'));
        $analysisResult->addRule(new Allowed($dependency, 'LayerA', 'LayerC'));

        $analysisResult->addRule(new Violation($dependency, 'LayerA', 'LayerC', new DummyViolationCreatingRule()));
        $analysisResult->addRule(new Violation($dependency, 'LayerB', 'LayerC', new DummyViolationCreatingRule()));

        $bufferedOutput = new BufferedOutput();

        $output = $this->createSymfonyOutput($bufferedOutput);
        $outputFormatterInput = new OutputFormatterInput(null, true, true, false);

        $mermaidJSOutputFormatter = new MermaidJSOutputFormatter(new FormatterConfiguration([
            'mermaidjs' => [
                'direction' => 'TD',
                'groups' => [
                    'User' => [
                        'LayerA',
                        'LayerB',
                    ],
                    'Admin' => [
                        'LayerC',
                        'LayerD',
                    ],
                ],
            ],
        ]));
        $mermaidJSOutputFormatter->finish(OutputResult::fromAnalysisResult($analysisResult), $output, $outputFormatterInput);
        $this->assertSame($expected, $bufferedOutput->fetch());
    }

    public function dataForTestFinish(): iterable
    {
        yield [
            'expected' => file_get_contents(__DIR__.'/data/mermaidjs-expected.txt'),
        ];
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
