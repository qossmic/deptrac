<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GraphVizOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertEquals('graphviz', (new GraphVizOutputFormatter())->getName());
    }

    public function testFinish(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $fileOccurrenceA = FileOccurrence::fromFilepath('classA.php', 0);
        $classA = ClassLikeName::fromFQCN('ClassA');

        $context = new Context([
            new Violation(new Dependency($classA, ClassLikeName::fromFQCN('ClassB'), $fileOccurrenceA), 'LayerA', 'LayerB'),
            new Violation(new Dependency($classA, ClassLikeName::fromFQCN('ClassHidden'), $fileOccurrenceA), 'LayerA', 'LayerHidden'),
            new Violation(new Dependency(ClassLikeName::fromFQCN('ClassAB'), ClassLikeName::fromFQCN('ClassBA'), FileOccurrence::fromFilepath('classAB.php', 1)), 'LayerA', 'LayerB'),
            new Allowed(new Dependency($classA, ClassLikeName::fromFQCN('ClassC'), $fileOccurrenceA), 'LayerA', 'LayerC'),
            new Uncovered(new Dependency($classA, ClassLikeName::fromFQCN('ClassD'), $fileOccurrenceA), 'LayerC'),
        ], [], []);

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            [
                GraphVizOutputFormatter::DISPLAY => false,
                GraphVizOutputFormatter::DUMP_IMAGE => false,
                GraphVizOutputFormatter::DUMP_DOT => $dotFile,
                GraphVizOutputFormatter::DUMP_HTML => false,
            ],
            [
                'hidden_layers' => [
                    'LayerHidden',
                ],
            ]
        );

        (new GraphVizOutputFormatter())->finish($context, $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf("Script dumped to %s\n", $dotFile), $bufferedOutput->fetch());
        self::assertFileEquals(__DIR__.'/data/graphviz-expected.dot', $dotFile);

        unlink($dotFile);
    }

    public function testGroups(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $dependency = new Dependency(
            ClassLikeName::fromFQCN('ClassA'),
            ClassLikeName::fromFQCN('ClassC'),
            FileOccurrence::fromFilepath('classA.php', 0)
        );

        $context = new Context([
                new Allowed($dependency, 'LayerA_A', 'LayerA_B'),
                new Allowed($dependency, 'LayerB_A', 'LayerB_B'),
                new Allowed($dependency, 'LayerA_A', 'LayerB_A'),
                new Allowed($dependency, 'LayerA_B', 'LayerB_A'),
        ], [], []);

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            [
                GraphVizOutputFormatter::DISPLAY => false,
                GraphVizOutputFormatter::DUMP_IMAGE => false,
                GraphVizOutputFormatter::DUMP_DOT => $dotFile,
                GraphVizOutputFormatter::DUMP_HTML => false,
            ],
            [
                'groups' => [
                    'groupA' => [
                        'LayerA_A',
                        'LayerA_B',
                    ],
                    'groupB' => [
                        'LayerB_A',
                        'LayerB_B',
                    ],
                ],
            ]
        );

        (new GraphVizOutputFormatter())->finish($context, $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf("Script dumped to %s\n", $dotFile), $bufferedOutput->fetch());
        self::assertFileEquals(__DIR__.'/data/graphviz-groups.dot', $dotFile);

        unlink($dotFile);
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
