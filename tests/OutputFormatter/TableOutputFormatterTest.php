<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\OutputFormatter\TableOutputFormatter;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Error;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Qossmic\Deptrac\RulesetEngine\Warning;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class TableOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertEquals('table', (new TableOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        $originalA = ClassLikeName::fromFQCN('OriginalA');
        $originalB = ClassLikeName::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                        AstInherit::newExtends(
                            ClassLikeName::fromFQCN('ClassInheritA'),
                            FileOccurrence::fromFilepath('originalA.php', 3)
                        )
                            ->withPath(
                                [
                                    AstInherit::newExtends(
                                        ClassLikeName::fromFQCN('ClassInheritB'),
                                        FileOccurrence::fromFilepath('originalA.php', 4)
                                    ),
                                    AstInherit::newExtends(
                                        ClassLikeName::fromFQCN('ClassInheritC'),
                                        FileOccurrence::fromFilepath('originalA.php', 5)
                                    ),
                                    AstInherit::newExtends(
                                        ClassLikeName::fromFQCN('ClassInheritD'),
                                        FileOccurrence::fromFilepath('originalA.php', 6)
                                    ),
                                ]
                            )
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            ' ----------- ------------------------------------------- 
  Reason      LayerA                                     
 ----------- ------------------------------------------- 
  Violation   ClassA must not depend on ClassB (LayerB)  
              ClassInheritD::6 ->                        
              ClassInheritC::5 ->                        
              ClassInheritB::4 ->                        
              ClassInheritA::3 ->                        
              OriginalB::12                              
              originalA.php:12                           
 ----------- ------------------------------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           1    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
        ];

        yield [
            [
                new Violation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            ' ----------- ------------------------------------------------- 
  Reason      LayerA                                           
 ----------- ------------------------------------------------- 
  Violation   OriginalA must not depend on OriginalB (LayerB)  
              originalA.php:12                                 
 ----------- ------------------------------------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           1    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
        ];

        yield [
            [],
            [],
            'warnings' => [],
            '
 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
        ];

        yield 'skipped violations' => [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            ' --------- ------------------------------------------------- 
  Reason    LayerA                                           
 --------- ------------------------------------------------- 
  Skipped   OriginalA must not depend on OriginalB (LayerB)  
            originalA.php:12                                 
 --------- ------------------------------------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   1    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
        ];

        yield 'skipped violations without reporting' => [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            '
 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   1    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
            'reportUncovered' => true,
            'reportSkipped' => false,
        ];

        yield 'uncovered' => [
            'rules' => [
                new Uncovered(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            'expectedOutput' => ' ----------- ------------------------------------------------- 
  Reason      LayerA                                           
 ----------- ------------------------------------------------- 
  Uncovered   OriginalA has uncovered dependency on OriginalB  
              originalA.php:12                                 
 ----------- ------------------------------------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            1    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
        ];

        yield 'uncovered without reporting' => [
            'rules' => [
                new Uncovered(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            'expectedOutput' => '
 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            1    
  Allowed              0    
  Warnings             0    
  Errors               0    
 -------------------- ----- 

',
            'reportUncovered' => false,
        ];

        yield 'an error occurred' => [
            [],
            [new Error('an error occurred')],
            'warnings' => [],
            ' ------------------- 
  Errors             
 ------------------- 
  an error occurred  
 ------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
  Warnings             0    
  Errors               1    
 -------------------- ----- 

',
        ];

        yield 'an warning occurred' => [
            'rules' => [],
            'errors' => [],
            'warnings' => [Warning::classLikeIsInMoreThanOneLayer(ClassLikeName::fromFQCN('Foo\Bar'), ['Layer 1', 'Layer 2'])],
            ' ------------------------------------------------------------------------------------------------------------------------- 
  Warnings                                                                                                                 
 ------------------------------------------------------------------------------------------------------------------------- 
  Foo\Bar is in more than one layer ["Layer 1", "Layer 2"]. It is recommended that one class should only be in one layer.  
 ------------------------------------------------------------------------------------------------------------------------- 


 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
  Warnings             1    
  Errors               0    
 -------------------- ----- 

',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, array $errors, array $warnings, string $expectedOutput, bool $reportUncovered = true, bool $reportSkipped = true): void
    {
        $bufferedOutput = new BufferedOutput();
        $output = new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );

        $formatter = new TableOutputFormatter();
        $formatter->finish(
            new Context($rules, $errors, $warnings, []),
            $output,
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => $reportUncovered,
                AnalyzeCommand::OPTION_REPORT_SKIPPED => $reportSkipped,
                AnalyzeCommand::OPTION_FAIL_ON_UNCOVERED => false,
            ])
        );

        static::assertEquals($expectedOutput, $bufferedOutput->fetch());
    }

    public function testGetOptions(): void
    {
        static::assertCount(0, (new TableOutputFormatter())->configureOptions());
    }

    public function testConsoleOutputFormatterIsEnabledByDefault(): void
    {
        static::assertTrue((new TableOutputFormatter())->enabledByDefault());
    }
}
