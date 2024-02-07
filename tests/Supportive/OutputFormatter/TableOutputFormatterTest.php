<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Ast\DependencyContext;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Contract\Result\Warning;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\TableOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data\DummyViolationCreatingRule;

use const PHP_EOL;

class TableOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertSame('table', (new TableOutputFormatter())->getName());
    }

    public static function basicDataProvider(): iterable
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('originalA.php', 3),
                            AstInheritType::EXTENDS
                        ))
                            ->replacePath(
                                [
                                    new AstInherit(
                                        ClassLikeToken::fromFQCN('ClassInheritB'),
                                        new FileOccurrence('originalA.php', 4),
                                        AstInheritType::EXTENDS
                                    ),
                                    new AstInherit(
                                        ClassLikeToken::fromFQCN('ClassInheritC'),
                                        new FileOccurrence('originalA.php', 5),
                                        AstInheritType::EXTENDS
                                    ),
                                    new AstInherit(
                                        ClassLikeToken::fromFQCN('ClassInheritD'),
                                        new FileOccurrence('originalA.php', 6),
                                        AstInheritType::EXTENDS
                                    ),
                                ]
                            )
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            [],
            'warnings' => [],
            ' ----------- ---------------------------------- 
  Reason      LayerA                            
 ----------- ---------------------------------- 
  DummyRule   ClassA must not depend on ClassB  
              Why? Because! (LayerB)            
              ClassInheritD::6 ->               
              ClassInheritC::5 ->               
              ClassInheritB::4 ->               
              ClassInheritA::3 ->               
              OriginalB::12                     
              originalA.php:12                  
 ----------- ---------------------------------- 


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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            [],
            'warnings' => [],
            ' ----------- ---------------------------------------- 
  Reason      LayerA                                  
 ----------- ---------------------------------------- 
  DummyRule   OriginalA must not depend on OriginalB  
              Why? Because! (LayerB)                  
              originalA.php:12                        
 ----------- ---------------------------------------- 


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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
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
                    new Dependency($originalA, $originalB, new DependencyContext(new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER)),
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
            'warnings' => [Warning::tokenIsInMoreThanOneLayer(ClassLikeToken::fromFQCN('Foo\Bar')->toString(), ['Layer 1', 'Layer 2'])],
            ' ------------------------------------------------------------------------------------------------------------------------- 
  Warnings                                                                                                                 
 ------------------------------------------------------------------------------------------------------------------------- 
  Foo\Bar is in more than one layer ["Layer 1", "Layer 2"]. It is recommended that one token should only be in one layer.  
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

        $analysisResult = new AnalysisResult();
        foreach ($rules as $rule) {
            $analysisResult->addRule($rule);
        }
        foreach ($errors as $error) {
            $analysisResult->addError($error);
        }
        foreach ($warnings as $warning) {
            $analysisResult->addWarning($warning);
        }

        $formatter = new TableOutputFormatter();
        $formatter->finish(
            OutputResult::fromAnalysisResult($analysisResult),
            $output,
            new OutputFormatterInput(
                null,
                $reportSkipped,
                $reportUncovered,
                false
            )
        );

        static::assertSame(str_replace("\n", PHP_EOL, $expectedOutput), $bufferedOutput->fetch());
    }
}
