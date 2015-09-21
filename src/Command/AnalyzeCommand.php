<?php

namespace DependencyTracker\Command;


use DependencyTracker\CollectionMap;
use DependencyTracker\Collectors\BasicCollectorVisitor;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PhpParser\NodeVisitor\NameResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AnalyzeCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('analyze')
            ->setDescription('Greet someone')
            ->addArgument(
                'dir',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;

        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(
            new BasicCollectorVisitor($map = new CollectionMap())
        );

        $f = new Filesystem();

        foreach ((new Finder)->in(
                     __DIR__ . '/../../' . $input->getArgument('dir')
                 )->files() as $file) {

            /**
             * @var $file SplFileInfo:
             */

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

} 