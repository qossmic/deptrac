<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMapGenerator;
use DependencyTracker\ClassLayerMap;
use DependencyTracker\Collector\ClassNameCollector;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\DependencyResult;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatter\GraphVizOutputFormatter;
use DependencyTracker\OutputFormatterFactory;
use DependencyTracker\Visitor\BasicDependencyVisitor;
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

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AstMapGenerator $astMapGenerator,
        ConfigurationLoader $configurationLoader,
        OutputFormatterFactory $formatterFactory
    )
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->astMapGenerator = $astMapGenerator;
        $this->configurationLoader = $configurationLoader;
        $this->formatterFactory = $formatterFactory;
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
            return;
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

        foreach($config->getViews() as $configurationView) {
            foreach($configurationView->getLayers() as $configurationLayer) {
                foreach($configurationLayer->getCollectors() as $configurationCollector) {

                    $collector = $this->getCollectorByType(
                        $configurationCollector->getType(),
                        $configurationCollector->getArgs(),
                        $configurationLayer
                    );

                    $output->writeln("collecting dependencies...");
                    $collector->applyAstFile($astMap, $dependencyResult);

                }
            }
        }

        $output->writeln("formatting dependencies.");
        $formatter->finish($dependencyResult);

        foreach ($dependencyResult->getDependencies() as $dependency) {
            $output->writeln(sprintf("%s::%s depends on %s", $dependency->getClassA(), $dependency->getClassALine(), $dependency->getClassB()));
        }
        $output->writeln("-------");
        foreach ($dependencyResult->getClassLayerMap() as $klass => $layers) {
            $output->writeln(sprintf("%s is in layers [%s]", $klass, implode(' ,',$layers)));
        }
    }

    private function getCollectorByType($type, $config, Configuration\ConfigurationLayer $configurationLayer)
    {
        if($type == "className") {
            $classNameCollector = new ClassNameCollector(
                $configurationLayer,
                $config
            );

            return $classNameCollector;
        }

        throw new \LogicException(sprintf(
            "Unknown Collector %s",
            $type
        ));
    }

} 