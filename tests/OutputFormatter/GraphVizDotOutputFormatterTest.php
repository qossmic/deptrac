<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\OutputFormatter\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\OutputFormatter\GraphVizOutputDotFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Result\Allowed;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GraphVizDotOutputFormatterTest extends TestCase
{
    public function testFinish(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $fileOccurrenceA = FileOccurrence::fromFilepath('classA.php', 0);
        $classA = ClassLikeToken::fromFQCN('ClassA');

        $context = new LegacyResult([
            new Violation(new Dependency($classA, ClassLikeToken::fromFQCN('ClassB'), $fileOccurrenceA), 'LayerA', 'LayerB'),
            new Violation(new Dependency($classA, ClassLikeToken::fromFQCN('ClassHidden'), $fileOccurrenceA), 'LayerA', 'LayerHidden'),
            new Violation(new Dependency(ClassLikeToken::fromFQCN('ClassAB'), ClassLikeToken::fromFQCN('ClassBA'), FileOccurrence::fromFilepath('classAB.php', 1)), 'LayerA', 'LayerB'),
            new Allowed(new Dependency($classA, ClassLikeToken::fromFQCN('ClassC'), $fileOccurrenceA), 'LayerA', 'LayerC'),
            new Uncovered(new Dependency($classA, ClassLikeToken::fromFQCN('ClassD'), $fileOccurrenceA), 'LayerC'),
        ], [], []);

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            $dotFile,
            false,
            false,
            false,
        );

        (new GraphVizOutputDotFormatter(new FormatterConfiguration([
            'graphviz' => [
                'hidden_layers' => [
                    'LayerHidden',
                ],
            ],
        ])))->finish($context, $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf("Script dumped to %s\n", $dotFile), $bufferedOutput->fetch());
        self::assertFileEquals(__DIR__.'/data/graphviz-expected.dot', $dotFile);

        unlink($dotFile);
    }

    public function testGroups(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('ClassA'),
            ClassLikeToken::fromFQCN('ClassC'),
            FileOccurrence::fromFilepath('classA.php', 0)
        );

        $context = new LegacyResult([
            new Allowed($dependency, 'User Frontend', 'User Backend'),
            new Allowed($dependency, 'Admin', 'Admin Backend'),
            new Allowed($dependency, 'User Frontend', 'Admin'),
            new Allowed($dependency, 'User Backend', 'Admin'),
        ], [], []);

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            $dotFile,
            false,
            false,
            false,
        );

        (new GraphVizOutputDotFormatter(new FormatterConfiguration([
            'graphviz' => [
                'groups' => [
                    'User' => [
                        'User Frontend',
                        'User Backend',
                    ],
                    'Admin' => [
                        'Admin',
                        'Admin Backend',
                    ],
                ],
            ],
        ])))->finish($context, $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf("Script dumped to %s\n", $dotFile), $bufferedOutput->fetch());
        self::assertFileEquals(__DIR__.'/data/graphviz-groups.dot', $dotFile);

        unlink($dotFile);
    }

    public function testPointToGroups(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('ClassA'),
            ClassLikeToken::fromFQCN('ClassC'),
            FileOccurrence::fromFilepath('classA.php', 0)
        );

        $context = new LegacyResult([
            new Allowed($dependency, 'User Frontend', 'User Backend'),
            new Allowed($dependency, 'Admin', 'Admin Backend'),
            new Allowed($dependency, 'User Frontend', 'Admin'),
            new Allowed($dependency, 'User Backend', 'Admin'),
        ], [], []);

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            $dotFile,
            false,
            false,
            false,
        );

        (new GraphVizOutputDotFormatter(new FormatterConfiguration([
            'graphviz' => [
                'groups' => [
                    'User' => [
                        'User Frontend',
                        'User Backend',
                    ],
                    'Admin' => [
                        'Admin',
                        'Admin Backend',
                    ],
                ],
                'pointToGroups' => true,
            ],
        ])))->finish($context, $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf("Script dumped to %s\n", $dotFile), $bufferedOutput->fetch());
        self::assertFileEquals(__DIR__.'/data/graphviz-groups-point.dot', $dotFile);

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
