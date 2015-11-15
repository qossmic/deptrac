<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMapGenerator;
use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\DependencyEmitter\DependencyEmitterInterface;
use DependencyTracker\DependencyEmitter\InheritanceDependencyEmitter;
use DependencyTracker\DependencyEmitter\UseDependencyEmitter;
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
        #    new InheritanceDependencyEmitter(),
            new UseDependencyEmitter()
        ];

        foreach ($dependencyEmitters as $dependencyEmitter) {
            $output->writeln(sprintf('start flatten dependencies <info>"%s"</info>', $dependencyEmitter->getName()));
            $dependencyEmitter->applyDependencies(
                $parser,
                $astMap,
                $dependencyResult
            );
        }
        $output->writeln("end flatten");

        foreach ($configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {

                $collector = $this->collectorFactory->getCollector($configurationCollector->getType());

                $output->writeln(
                    sprintf(
                        'collecting <info>"%s"</info> dependencies for layer <info>"%s"</info>',
                        $configurationCollector->getType(),
                        $configurationLayer->getName()
                    )
                );

                foreach ($astMap->getAstClassReferences() as $astClassReference) {

                    if ($collector->satisfy(
                        $configurationCollector->getArgs(),
                        $astClassReference,
                        $this->collectorFactory
                    )) {
                        $dependencyResult->addClassToLayer(
                            $astClassReference->getClassName(),
                            $configurationLayer->getName()
                        );
                    }
                }
            }
        }

        $output->writeln("formatting dependencies.");
        $formatter->finish($dependencyResult);


        # collect violations
        /** @var $violations RulesetEngine\RulesetViolation[] */
        $violations = $this->rulesetEngine->getViolations($dependencyResult, $configuration->getRuleset());
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

            if ($violation->getDependeny() instanceof DependencyResult\InheritDependency) {
                $output->writeln(
                    sprintf(
                        "<info>%s</info> inherits <info>%s</info>::%s which must not depend on <info>%s</info> (%s on %s)",
                        $violation->getDependeny()->getClassInheritedOver(),
                        $violation->getDependeny()->getClassA(),
                        $violation->getDependeny()->getClassALine(),
                        $violation->getDependeny()->getClassB(),
                        $violation->getLayerA(),
                        $violation->getLayerB()
                    )
                );
            } else {
                $output->writeln(
                    sprintf(
                        "<info>%s</info>::%s must not depend on <info>%s</info> (%s on %s)",
                        $violation->getDependeny()->getClassA(),
                        $violation->getDependeny()->getClassALine(),
                        $violation->getDependeny()->getClassB(),
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