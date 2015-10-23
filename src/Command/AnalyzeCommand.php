<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMap;
use DependencyTracker\CollectionMap;
use DependencyTracker\Collector\DebugCollector;
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

        $files = iterator_to_array((new Finder)->in(
            __DIR__ . '/../../' . $input->getArgument('dir')
        )->files());

        new ConsoleFormatter($this->dispatcher, $output);

        // Step1
        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(count($files)));
        $astMap = $this->createAstMapByFiles($files);
        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($astMap));

        // Step2
        new DebugCollector($this->dispatcher);
        (new BasicDependencyVisitor($this->dispatcher))->analyze($astMap);
    }

    private function createAstMapByFiles(array $files)
    {
        $map = [];
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver());

        foreach ($files as $file) {

            /** @var $file SplFileInfo: */

            try {
                $code = file_get_contents($file->getPathname());
                $map[$file->getPathname()] = $ast = $traverser->traverse($parser->parse($code));
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

        return new AstMap($map);
    }

    /*

    protected function execute2(
        InputInterface $input,
        OutputInterface $output
    ) {
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;

        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(
            new BasicCollectorVisitor()
        );

        $f = new Filesystem();

        foreach ((new Finder)->in(__DIR__ . '/../../' . $input->getArgument('dir'))->files() as $file) {



            try {
                $code = file_get_contents($file->getPathname());

                // parse
                $stmts = $parser->parse($code);

                // traverse
                $stmts = $traverser->traverse($stmts);

            } catch (\PhpParser\Error $e) {
                $output->writeln(
                    '<error>Parse Error: ' . $file->getPathname().' - '. $e->getMessage(
                    ) . '</error>'
                );
            }
        }


        $graph = new \Fhaculty\Graph\Graph();
        $vertices = [];

        foreach ($map->getDependencies() as $from => $t) {

            foreach ($t as $to) {
                if (!isset($vertices[$to])) {
                    $vertices[$to] = $graph->createVertex($to);
                }
            }

            if (!isset($vertices[$from])) {
                $vertices[$from] = $graph->createVertex($from);
            }
        }

        foreach ($map->getDependencies() as $from => $t) {
            foreach ($t as $to) {
                $vertices[$from]->createEdgeTo($vertices[$to]);
            }
        }

        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);

        #var_dump($map->getDependencies());
    }
    */

} 