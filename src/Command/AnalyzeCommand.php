<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMapGenerator;
use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\DependencyResult;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatterFactory;
use DependencyTracker\RulesetEngine;
use DependencyTracker\Visitor\BasicDependencyVisitor;
use DependencyTracker\Visitor\InheritanceDependencyVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyzeCommand extends Command
{
    protected $dispatcher;

    protected $astMapGenerator;

    protected $configurationLoader;

    protected $formatterFactory;

    protected $rulesetEngine;

    protected $collectorFactory;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AstMapGenerator $astMapGenerator,
        ConfigurationLoader $configurationLoader,
        OutputFormatterFactory $formatterFactory,
        RulesetEngine $rulesetEngine,
        CollectorFactory $collectorFactory
    )
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->astMapGenerator = $astMapGenerator;
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

        $config = $this->configurationLoader->loadConfiguration();

        new ConsoleFormatter($this->dispatcher, $output);


        $formatter = $this->formatterFactory->getFormatterByName($config->getFormatter());

        // generate astMap
        $astMap = $this->astMapGenerator->generateAstMap($config, $output);

        $dependencyResult = new DependencyResult();

        $output->writeln("analyzing dependencies");
        (new BasicDependencyVisitor($dependencyResult))->analyze($astMap);
        $output->writeln("end analyzing dependencies");
        $output->writeln("flatten dependencies");
        (new InheritanceDependencyVisitor())->flattenInheritanceDependencies($astMap, $dependencyResult);
        $output->writeln("end flatten dependencies");

        foreach($config->getLayers() as $configurationLayer) {
            foreach($configurationLayer->getCollectors() as $configurationCollector) {

                $output->writeln(sprintf(
                    'collecting <info>"%s"</info> dependencies for layer <info>"%s"</info>',
                    $configurationCollector->getType(),
                    $configurationLayer->getName()
                ));

                $this->collectorFactory->getCollector(
                    $configurationCollector->getType()
                )->applyAstFile(
                    $astMap,
                    $dependencyResult,
                    $configurationLayer,
                    $configurationCollector->getArgs()
                );
            }
        }

        $output->writeln("formatting dependencies.");
        $formatter->finish($dependencyResult);


        # collect violations
        /** @var $violations RulesetEngine\RulesetViolation[] */
        $violations = $this->rulesetEngine->getViolations($dependencyResult, $config->getRuleset());
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
                $output->writeln(sprintf(
                    "class <info>%s</info>::%s inherits <info>%s</info>::%s which must not depend on class <info>%s</info> (%s on %s)",
                    $violation->getDependeny()->getClassInheritedOver(),
                    $violation->getDependeny()->getClassInheritedOverLine(),
                    $violation->getDependeny()->getClassA(),
                    $violation->getDependeny()->getClassALine(),
                    $violation->getDependeny()->getClassB(),
                    $violation->getLayerA(),
                    $violation->getLayerB()
                ));
            } else {
                $output->writeln(sprintf(
                    "class <info>%s</info>::%s must not depend on class <info>%s</info> (%s on %s)",
                    $violation->getDependeny()->getClassA(),
                    $violation->getDependeny()->getClassALine(),
                    $violation->getDependeny()->getClassB(),
                    $violation->getLayerA(),
                    $violation->getLayerB()
                ));
            }
        }

        $output->writeln(sprintf(
            "\nFound <error>%s Violations</error>",
            count($violations)
        ));
    }

} 