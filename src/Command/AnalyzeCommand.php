<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMap;
use DependencyTracker\Collector\CollectorInterface;
use DependencyTracker\Collector\DebugCollector;
use DependencyTracker\Configuration;
use DependencyTracker\Event\AstFileAnalyzedEvent;
use DependencyTracker\Event\AstFileSyntaxErrorEvent;
use DependencyTracker\Event\PostCreateAstMapEvent;
use DependencyTracker\Event\PreCreateAstMapEvent;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\Visitor\BasicDependencyVisitor;
use PhpParser\NodeVisitor\NameResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class AnalyzeCommand extends Command
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
    }

    protected function configure()
    {
        $this
            ->setName('analyze')
            ->setDescription('Greet someone')
            ->addArgument(
                'dir',
                InputArgument::OPTIONAL
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        ini_set('memory_limit', -1);

        $config = Configuration::fromArray(
            Yaml::parse(file_get_contents(__DIR__.'/../../depfile.yml'))
        );

        new ConsoleFormatter($this->dispatcher, $output);


        // Step1
        $files = [];
        foreach($config->getPaths() as $path) {
            $files = array_merge(iterator_to_array(
                (new Finder)->in(__DIR__ . '/../../' . $path)
                    ->name('*.php')
                    ->files()
            ), $files);
        }

        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(count($files)));
        $this->createAstMapByFiles($astMap = new AstMap(), $files);
        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($astMap));


        // Step2 Register Collectors
        /** @var $collectors CollectorInterface[] */
        $collectors = [];
        foreach($config->getViews() as $configurationView) {
            foreach($configurationView->getLayers() as $configurationLayer) {
                foreach($configurationLayer->getCollectors() as $configurationCollector) {

                    if($configurationCollector->getType() == "debug") {
                         $collectors[] = new DebugCollector(
                            $this->dispatcher,
                            $configurationLayer,
                            $configurationCollector->getArgs()
                        );

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


    }

    private function createAstMapByFiles(AstMap $astMap, array $files)
    {
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver());

        foreach ($files as $file) {

            /** @var $file SplFileInfo: */

            try {
                $code = file_get_contents($file->getPathname());
                $astMap->add($file->getPathname(), $ast = $traverser->traverse($parser->parse($code)));
                $this->dispatcher->dispatch(
                    AstFileAnalyzedEvent::class,
                    new AstFileAnalyzedEvent(
                        $file, $ast
                    )
                );

            } catch (\PhpParser\Error $e) {
                $this->dispatcher->dispatch(
                    AstFileSyntaxErrorEvent::class,
                    new AstFileSyntaxErrorEvent(
                        $file, $e->getMessage()
                    )
                );
            }
        }
    }

} 