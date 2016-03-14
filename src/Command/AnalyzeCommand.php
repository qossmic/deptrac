<?php

namespace SensioLabs\Deptrac\Command;

use SensioLabs\Deptrac\ClassNameLayerResolver;
use SensioLabs\Deptrac\ClassNameLayerResolverCacheDecorator;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\Deptrac\Configuration;
use SensioLabs\Deptrac\ConfigurationLoader;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyEmitter\BasicDependencyEmitter;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;
use SensioLabs\Deptrac\DependencyInheritanceFlatter;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\Formatter\ConsoleFormatter;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\RulesetEngine;
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
        $this->dispatcher = $dispatcher;
        $this->astRunner = $astRunner;
        $this->formatterFactory = $formatterFactory;
        $this->rulesetEngine = $rulesetEngine;
        $this->collectorFactory = $collectorFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('analyze');

        $this->getDefinition()->setArguments([
            new InputArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile', getcwd().'/depfile.yml'),
        ]);

        $this->getDefinition()->addOptions($this->formatterFactory->getFormatterOptions());
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        ini_set('memory_limit', -1);

        $this->printBanner($output);

        $configurationLoader = new ConfigurationLoader($input->getArgument('depfile'));

        if (!$configurationLoader->hasConfiguration()) {
            $this->printConfigMissingError($output, $configurationLoader);

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
            $this->printEmitStart($output, $dependencyEmitter);
            $dependencyEmitter->applyDependencies(
                $parser,
                $astMap,
                $dependencyResult
            );
        }
        $this->printEmitEnd($output);
        $this->printFlattenStart($output);

        (new DependencyInheritanceFlatter())->flattenDependencies($astMap, $dependencyResult);

        $this->printFlattenEnd($output);

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorFactory)
        );

        $this->printCollectViolations($output);

        /** @var $violations RulesetEngine\RulesetViolation[] */
        $violations = $this->rulesetEngine->getViolations($dependencyResult, $classNameLayerResolver, $configuration->getRuleset());

        $this->printFormattingStart($output);

        foreach ($this->formatterFactory->getActiveFormatters($input) as $formatter) {
            try {
                $formatter->finish(
                    new DependencyContext($astMap, $violations, $dependencyResult, $classNameLayerResolver),
                    $output,
                    $this->formatterFactory->getOutputFormatterInput($formatter, $input)
                );
            } catch (\Exception $ex) {
                $this->printFormatterException($output, $formatter->getName(), $ex);
            }
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

    /**
     * @param OutputInterface $output
     */
    protected function printBanner(OutputInterface $output)
    {
        $output->writeln("\n<comment>deptrac is alpha, not production ready.\nplease help us and report feedback / bugs.</comment>\n");
    }

    /**
     * @param OutputInterface $output
     * @param $configurationLoader
     */
    protected function printConfigMissingError(OutputInterface $output, $configurationLoader)
    {
        $output->writeln(sprintf('depfile "%s" not found, run "deptrac init" to create one.', $configurationLoader->getConfigFilePathname()));
    }

    /**
     * @param OutputInterface $output
     * @param $dependencyEmitter
     */
    protected function printEmitStart(OutputInterface $output, $dependencyEmitter)
    {
        $output->writeln(sprintf('start emitting dependencies <info>"%s"</info>', $dependencyEmitter->getName()));
    }

    /**
     * @param OutputInterface $output
     */
    protected function printEmitEnd(OutputInterface $output)
    {
        $output->writeln('<info>end emitting dependencies</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function printFlattenStart(OutputInterface $output)
    {
        $output->writeln('<info>start flatten dependencies</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function printFlattenEnd(OutputInterface $output)
    {
        $output->writeln('<info>end flatten dependencies</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function printCollectViolations(OutputInterface $output)
    {
        $output->writeln('<info>collecting violations.</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function printFormattingStart(OutputInterface $output)
    {
        $output->writeln('<info>formatting dependencies.</info>');
    }

    /**
     * @param OutputInterface $output
     * @param $formatterName
     * @param \Exception $exception
     */
    protected function printFormatterException(OutputInterface $output, $formatterName, \Exception $exception)
    {
        $output->writeln('');
        $errorMessages = [
            '',
            sprintf('Output formatter %s threw an Exception:', $formatterName),
            sprintf('Message: %s', $exception->getMessage()),
            '',
        ];
        $output->writeln($this->getHelper('formatter')->formatBlock($errorMessages, 'error'));
        $output->writeln('');
    }
}
