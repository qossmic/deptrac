<?php

namespace DependencyTracker\Command;

use DependencyTracker\ClassNameLayerResolver;
use DependencyTracker\ClassNameLayerResolverCacheDecorator;
use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration;
use DependencyTracker\ConfigurationLoader;
use DependencyTracker\DependencyEmitter\BasicDependencyEmitter;
use DependencyTracker\DependencyEmitter\DependencyEmitterInterface;
use DependencyTracker\DependencyEmitter\InheritanceDependencyEmitter;
use DependencyTracker\DependencyInheritanceFlatter;
use DependencyTracker\DependencyResult;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatterFactory;
use DependencyTracker\RulesetEngine;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\AstRunner\AstRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class AnalyzeCommand extends Command
{
    protected $dispatcher;

    protected $astRunner;

    protected $formatterFactory;

    protected $rulesetEngine;

    protected $collectorFactory;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AstRunner $astRunner,
        OutputFormatterFactory $formatterFactory,
        RulesetEngine $rulesetEngine,
        CollectorFactory $collectorFactory
    ) {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->astRunner = $astRunner;
        $this->formatterFactory = $formatterFactory;
        $this->rulesetEngine = $rulesetEngine;
        $this->collectorFactory = $collectorFactory;
    }

    protected function configure()
    {
        $this->setName('analyze');
        $this->addArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile', getcwd().'/depfile.yml');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        ini_set('memory_limit', -1);

        $output->writeln("\n<comment>deptrac is alpha, not production ready.\nplease help us and report feedback / bugs.</comment>\n");

        $configurationLoader = new ConfigurationLoader($input->getArgument('depfile'));

        if (!$configurationLoader->hasConfiguration()) {
            $output->writeln(sprintf('depfile "%s" not found, run "deptrac init" to create one.', $configurationLoader->getConfigFilePathname()));

            return 1;
        }

        $configuration = $configurationLoader->loadConfiguration();

        new ConsoleFormatter($this->dispatcher, $output);

        $parser = new NikicPhpParser();
        $astMap = $this->astRunner->createAstMapByFiles($parser, $this->dispatcher, $this->collectFiles($configuration));

        $dependencyResult = new DependencyResult();

        /** @var $dependencyEmitters DependencyEmitterInterface[] */
        $dependencyEmitters = [
            new InheritanceDependencyEmitter(),
            new BasicDependencyEmitter(),
        ];

        foreach ($dependencyEmitters as $dependencyEmitter) {
            $output->writeln(sprintf('start emitting dependencies <info>"%s"</info>', $dependencyEmitter->getName()));
            $dependencyEmitter->applyDependencies(
                $parser,
                $astMap,
                $dependencyResult
            );
        }
        $output->writeln('end emitting dependencies');
        $output->writeln('start flatten dependencies');

        (new DependencyInheritanceFlatter())->flattenDependencies($astMap, $dependencyResult);

        $output->writeln('end flatten dependencies');

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorFactory)
        );

        $output->writeln('collecting violations.');

        /** @var $violations RulesetEngine\RulesetViolation[] */
        $violations = $this->rulesetEngine->getViolations($dependencyResult, $classNameLayerResolver, $configuration->getRuleset());

        $output->writeln('formatting dependencies.');

        foreach (explode(',', $configuration->getFormatter()) as $formatterName) {
            $this->formatterFactory
                ->getFormatterByName(trim($formatterName))
                ->finish($astMap, $violations, $dependencyResult, $classNameLayerResolver, $output)
            ;
        }

        return !count($violations);
    }

    private function collectFiles(Configuration $configuration)
    {
        $files = iterator_to_array(
            (new Finder())
                ->in($configuration->getPaths())
                ->name('*.php')
                ->files()
                ->followLinks()
                ->ignoreUnreadableDirs(true)
                ->ignoreVCS(true)
        );

        return array_filter($files, function (\SplFileInfo $fileInfo) use ($configuration) {
            foreach ($configuration->getExcludeFiles() as $excludeFiles) {
                if (preg_match('/'.$excludeFiles.'/i', $fileInfo->getPathname())) {
                    return false;
                }
            }

            return true;
        });
    }
}
