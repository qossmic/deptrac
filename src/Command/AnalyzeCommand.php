<?php

namespace SensioLabs\Deptrac\Command;

use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\AstRunner\AstRunner;
use SensioLabs\Deptrac\ClassNameLayerResolver;
use SensioLabs\Deptrac\ClassNameLayerResolverCacheDecorator;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\Deptrac\Configuration\Exception\MissingFileException;
use SensioLabs\Deptrac\Configuration\Loader as ConfigurationLoader;
use SensioLabs\Deptrac\Dependency\Analyzer as DependencyAnalyzer;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\FileResolver;
use SensioLabs\Deptrac\Formatter\ConsoleFormatter;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\RulesetEngine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyzeCommand extends Command
{
    private $configurationLoader;
    private $fileResolver;
    private $dispatcher;
    private $astRunner;
    private $formatterFactory;
    private $rulesetEngine;
    private $collectorFactory;
    private $dependencyAnalyzer;

    public function __construct(
        ConfigurationLoader $configurationLoader,
        FileResolver $fileResolver,
        EventDispatcherInterface $dispatcher,
        AstRunner $astRunner,
        OutputFormatterFactory $formatterFactory,
        RulesetEngine $rulesetEngine,
        CollectorFactory $collectorFactory,
        DependencyAnalyzer $dependencyAnalyzer
    ) {
        $this->configurationLoader = $configurationLoader;
        $this->fileResolver = $fileResolver;
        $this->dispatcher = $dispatcher;
        $this->astRunner = $astRunner;
        $this->formatterFactory = $formatterFactory;
        $this->rulesetEngine = $rulesetEngine;
        $this->collectorFactory = $collectorFactory;
        $this->dependencyAnalyzer = $dependencyAnalyzer;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('analyze');
        $this->setAliases(['analyse']);

        $this->getDefinition()->setArguments([
            new InputArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile', getcwd().'/depfile.yml'),
        ]);

        $this->getDefinition()->addOptions($this->formatterFactory->getFormatterOptions());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);

        $this->printBanner($output);

        try {
            $configuration = $this->configurationLoader->load($input->getArgument('depfile'));
        } catch (MissingFileException $e) {
            $this->printConfigMissingError($output, $input->getArgument('depfile'));

            return 1;
        }

        new ConsoleFormatter($this->dispatcher, $output);

        $parser = new NikicPhpParser();
        $astMap = $this->astRunner->createAstMapByFiles($parser, $this->fileResolver->resolve($configuration));

        $dependencyResult = $this->dependencyAnalyzer->analyze($parser, $astMap);

        $classNameLayerResolver = new ClassNameLayerResolverCacheDecorator(
            new ClassNameLayerResolver($configuration, $astMap, $this->collectorFactory, $parser)
        );

        $this->printCollectViolations($output);

        /** @var RulesetEngine\RulesetViolation[] $violations */
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

        return count($violations) ? 1 : 0;
    }

    protected function printBanner(OutputInterface $output)
    {
        $output->writeln("\n<comment>deptrac is alpha, not production ready.\nplease help us and report feedback / bugs.</comment>\n");
    }

    protected function printConfigMissingError(OutputInterface $output, string $file)
    {
        $output->writeln(sprintf('depfile "%s" not found, run "deptrac init" to create one.', $file));
    }

    protected function printCollectViolations(OutputInterface $output)
    {
        $output->writeln('<info>collecting violations.</info>');
    }

    protected function printFormattingStart(OutputInterface $output)
    {
        $output->writeln('<info>formatting dependencies.</info>');
    }

    protected function printFormatterException(OutputInterface $output, string $formatterName, \Exception $exception)
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
