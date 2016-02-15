<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMapGenerator;
use DependencyTracker\ClassNameLayerResolver;
use DependencyTracker\ClassNameLayerResolverCacheDecorator;
use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\DependencyEmitter\BasicDependencyEmitter;
use DependencyTracker\DependencyEmitter\DependencyEmitterInterface;
use DependencyTracker\DependencyEmitter\InheritanceDependencyEmitter;
use DependencyTracker\DependencyEmitter\UseDependencyEmitter;
use DependencyTracker\DependencyInheritanceFlatter;
use DependencyTracker\DependencyResult;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatterFactory;
use DependencyTracker\RulesetEngine;
use DependencyTracker\Visitor\InheritanceDependencyVisitor;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\AstRunner\AstRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class AnalyzeCommand extends Command
{
    protected $dispatcher;

    protected $astRunner;

    protected $configurationLoader;

    protected $formatterFactory;

    protected $rulesetEngine;

    protected $collectorFactory;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AstRunner $astRunner,
        ConfigurationLoader $configurationLoader,
        OutputFormatterFactory $formatterFactory,
        RulesetEngine $rulesetEngine,
        CollectorFactory $collectorFactory
    ) {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->astRunner = $astRunner;
        $this->configurationLoader = $configurationLoader;
        $this->formatterFactory = $formatterFactory;
        $this->rulesetEngine = $rulesetEngine;
        $this->collectorFactory = $collectorFactory;
    }

    protected function configure()
    {
        $this->setName('analyze');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        ini_set('memory_limit', -1);

        if (!$this->configurationLoader->hasConfiguration()) {
            $output->writeln("depfile.yml not found, run dtrac init to create one.");

            return 1;
        }

        $configuration = $this->configurationLoader->loadConfiguration();

        new ConsoleFormatter($this->dispatcher, $output);


        $formatter = $this->formatterFactory->getFormatterByName($configuration->getFormatter());

        $parser = new NikicPhpParser();
        $astMap = $this->astRunner->createAstMapByFiles($parser, $this->dispatcher, $this->collectFiles($configuration));

        $dependencyResult = new DependencyResult();

        /** @var $dependencyEmitters DependencyEmitterInterface[] */

        $dependencyEmitters = [
            new InheritanceDependencyEmitter(),
            new BasicDependencyEmitter()
        ];

        foreach ($dependencyEmitters as $dependencyEmitter) {
            $output->writeln(sprintf('start emitting dependencies <info>"%s"</info>', $dependencyEmitter->getName()));
            $dependencyEmitter->applyDependencies(
                $parser,
                $astMap,
                $dependencyResult
            );
        }
        $output->writeln("end emitting dependencies");
        $output->writeln("start flatten dependencies");

        (new DependencyInheritanceFlatter())->flattenDependencies($astMap, $dependencyResult);

        $output->writeln("end flatten dependencies");

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorFactory)
        );

        $output->writeln("formatting dependencies.");
        $formatter->finish($dependencyResult, $classNameLayerResolver);


        # collect violations
        /** @var $violations RulesetEngine\RulesetViolation[] */
        $violations = $this->rulesetEngine->getViolations($dependencyResult, $classNameLayerResolver, $configuration->getRuleset());
        $this->displayViolations($violations, $output);

        return !count($violations);
    }

    /**
     * @param RulesetEngine\RulesetViolation[] $violations
     * @param OutputInterface $output
     */
    private function displayViolations(array $violations, OutputInterface $output)
    {
        foreach ($violations as $violation) {

            if ($violation->getDependency() instanceof DependencyResult\InheritDependency) {
                $output->writeln(
                    sprintf(
                        "<info>%s::%s</info> must not depend on <info>%s</info> (%s on %s) path: %s",
                        $violation->getDependency()->getClassA(),
                        $violation->getDependency()->getClassALine(),
                        $violation->getDependency()->getClassB(),
                        $violation->getLayerA(),
                        $violation->getLayerB(),
                        $violation->getDependency()->getPath()->__toString()
                    )
                );
            } else {
                $output->writeln(
                    sprintf(
                        '<info>%s</info>::%s must not depend on <info>%s</info> (%s on %s)',
                        $violation->getDependency()->getClassA(),
                        $violation->getDependency()->getClassALine(),
                        $violation->getDependency()->getClassB(),
                        $violation->getLayerA(),
                        $violation->getLayerB()
                    )
                );
            }
        }

        $output->writeln(
            sprintf(
                "\nFound <error>%s Violations</error>",
                count($violations)
            )
        );
    }


    private function collectFiles(Configuration $configuration)
    {
        $files = iterator_to_array(
            (new Finder)
                ->in($configuration->getPaths())
                ->name('*.php')
                ->files()
                ->followLinks()
                ->ignoreUnreadableDirs(true)
                ->ignoreVCS(true)
        );
        return array_filter($files, function(\SplFileInfo $fileInfo) use ($configuration) {
            foreach ($configuration->getExcludeFiles() as $excludeFiles) {
                if(preg_match('/'.$excludeFiles.'/i', $fileInfo->getPathname())) {
                    return false;
                }
            }
            return true;
        });
    }

} 