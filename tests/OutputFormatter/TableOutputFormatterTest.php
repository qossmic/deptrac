<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Console\Symfony\Style;
use SensioLabs\Deptrac\Console\Symfony\SymfonyOutput;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\TableOutputFormatter;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
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
 -------------------- ----- 

',
        ];

        yield [
            [],
            '
 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            0    
  Allowed              0    
 -------------------- ----- 

',
        ];

        yield [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
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
 -------------------- ----- 

',
        ];

        yield [
            [
                new Uncovered(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA'
                ),
            ],
            ' ----------- ------------------------------------------------- 
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
 -------------------- ----- 

',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, string $expectedOutput): void
    {
        $bufferedOutput = new BufferedOutput();
        $output = new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );

        $formatter = new TableOutputFormatter();
        $formatter->finish(
            new Context($rules),
            $output,
            new OutputFormatterInput(['report-uncovered' => true])
        );

        static::assertEquals($expectedOutput, $bufferedOutput->fetch());
    }

    public function testGetOptions(): void
    {
        static::assertCount(1, (new TableOutputFormatter())->configureOptions());
    }

    public function testConsoleOutputFormatterIsEnabledByDefault(): void
    {
        static::assertFalse((new TableOutputFormatter())->enabledByDefault());
    }
}
