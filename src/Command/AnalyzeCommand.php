<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMapGenerator;
use DependencyTracker\ClassLayerMap;
use DependencyTracker\Collector\ClassNameCollector;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatter\GraphVizOutputFormatter;
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

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AstMapGenerator $astMapGenerator,
        ConfigurationLoader $configurationLoader
    )
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->astMapGenerator = $astMapGenerator;
        $this->configurationLoader = $configurationLoader;
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


        // generate astMap
        $astMap = $this->astMapGenerator->generateAstMap($config, $output);

        // Step2 Register Collectors
        $formatters = [];

        foreach($config->getViews() as $configurationView) {

            $classLayerMap = new ClassLayerMap();

            $formatters[] = new GraphVizOutputFormatter(
                $this->dispatcher,
                $classLayerMap
            );

            foreach($configurationView->getLayers() as $configurationLayer) {
                foreach($configurationLayer->getCollectors() as $configurationCollector) {
                    if($configurationCollector->getType() == "className") {
                        $classNameCollector = new ClassNameCollector(
                            $configurationLayer,
                            $classLayerMap,
                            $configurationCollector->getArgs()
                        );

                        $classNameCollector->applyAstFile($astMap);

                        continue;
                    }

                    throw new \LogicException(sprintf(
                        "Unknown Collector %s",
                        $configurationCollector->getType()
                    ));

                }
            }
        }

        // Step3
        (new BasicDependencyVisitor($this->dispatcher))->analyze($astMap);

        foreach ($formatters as $formatter) {
            $formatter->finish();
        }


    }

} 