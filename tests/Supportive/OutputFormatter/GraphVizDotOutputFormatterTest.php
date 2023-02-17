<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\Allowed;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\Supportive\OutputFormatter\GraphVizOutputDotFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;

use const PHP_EOL;

final class GraphVizDotOutputFormatterTest extends TestCase
{
    public function testFinish(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $fileOccurrenceA = new FileOccurrence('classA.php', 0);
        $classA = ClassLikeToken::fromFQCN('ClassA');

        $analysisResult = new AnalysisResult();
        $analysisResult->addRule(new Violation(new Dependency($classA, ClassLikeToken::fromFQCN('ClassB'), $fileOccurrenceA, DependencyType::PARAMETER), 'LayerA', 'LayerB'));
        $analysisResult->addRule(new Violation(new Dependency($classA, ClassLikeToken::fromFQCN('ClassHidden'), $fileOccurrenceA, DependencyType::PARAMETER), 'LayerA', 'LayerHidden'));
        $analysisResult->addRule(new Violation(new Dependency(ClassLikeToken::fromFQCN('ClassAB'), ClassLikeToken::fromFQCN('ClassBA'),
            new FileOccurrence('classAB.php', 1), DependencyType::PARAMETER
        ), 'LayerA', 'LayerB'));
        $analysisResult->addRule(new Allowed(new Dependency($classA, ClassLikeToken::fromFQCN('ClassC'), $fileOccurrenceA, DependencyType::PARAMETER), 'LayerA', 'LayerC'));
        $analysisResult->addRule(new Uncovered(new Dependency($classA, ClassLikeToken::fromFQCN('ClassD'), $fileOccurrenceA, DependencyType::PARAMETER), 'LayerC'));

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
                'groups' => [],
                'point_to_groups' => false,
            ],
        ])))->finish(OutputResult::fromAnalysisResult($analysisResult), $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf('Script dumped to %s'.PHP_EOL, Path::normalize($dotFile)), Path::normalize($bufferedOutput->fetch()));

        $this->replaceWindowsLineEndings($dotFile);

        self::assertFileEquals(__DIR__.'/data/graphviz-expected.dot', $dotFile);

        unlink($dotFile);
    }

    public function testGroups(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('ClassA'),
            ClassLikeToken::fromFQCN('ClassC'), new FileOccurrence('classA.php', 0), DependencyType::PARAMETER
        );

        $analysisResult = new AnalysisResult();
        $analysisResult->addRule(new Allowed($dependency, 'User Frontend', 'User Backend'));
        $analysisResult->addRule(new Allowed($dependency, 'Admin', 'Admin Backend'));
        $analysisResult->addRule(new Allowed($dependency, 'User Frontend', 'Admin'));
        $analysisResult->addRule(new Allowed($dependency, 'User Backend', 'Admin'));

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            $dotFile,
            false,
            false,
            false,
        );

        (new GraphVizOutputDotFormatter(new FormatterConfiguration([
            'graphviz' => [
                'hidden_layers' => [],
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
                'point_to_groups' => false,
            ],
        ])))->finish(OutputResult::fromAnalysisResult($analysisResult), $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf('Script dumped to %s'.PHP_EOL, Path::normalize($dotFile)), Path::normalize($bufferedOutput->fetch()));

        $this->replaceWindowsLineEndings($dotFile);

        self::assertFileEquals(__DIR__.'/data/graphviz-groups.dot', $dotFile);

        unlink($dotFile);
    }

    public function testPointToGroups(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $dependency = new Dependency(
            ClassLikeToken::fromFQCN('ClassA'),
            ClassLikeToken::fromFQCN('ClassC'), new FileOccurrence('classA.php', 0), DependencyType::PARAMETER
        );

        $analysisResult = new AnalysisResult();
        $analysisResult->addRule(new Allowed($dependency, 'User Frontend', 'User Backend'));
        $analysisResult->addRule(new Allowed($dependency, 'Admin', 'Admin Backend'));
        $analysisResult->addRule(new Allowed($dependency, 'User Frontend', 'Admin'));
        $analysisResult->addRule(new Allowed($dependency, 'User Backend', 'Admin'));

        $bufferedOutput = new BufferedOutput();
        $input = new OutputFormatterInput(
            $dotFile,
            false,
            false,
            false,
        );

        (new GraphVizOutputDotFormatter(new FormatterConfiguration([
            'graphviz' => [
                'hidden_layers' => [],
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
                'point_to_groups' => true,
            ],
        ])))->finish(OutputResult::fromAnalysisResult($analysisResult), $this->createSymfonyOutput($bufferedOutput), $input);

        self::assertSame(sprintf('Script dumped to %s'.PHP_EOL, Path::normalize($dotFile)), Path::normalize($bufferedOutput->fetch()));

        $this->replaceWindowsLineEndings($dotFile);

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

    private function replaceWindowsLineEndings($expectedFile): void
    {
        if ("\n" !== PHP_EOL) {
            // fix line endings on windows to match with the files in git
            file_put_contents($expectedFile, str_replace(PHP_EOL, "\n", file_get_contents($expectedFile)));
        }
    }
}
