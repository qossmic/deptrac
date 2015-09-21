<?php

namespace DependencyTracker\Command;


use DependencyTracker\CollectionMap;
use DependencyTracker\Collectors\BasicCollectorVisitor;
use DependencyTracker\Configuration;
use DependencyTracker\GraphDrawer;
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

        $config = new Configuration();

        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(
            new BasicCollectorVisitor($map = new CollectionMap(
                $config->
            ))
        );




        foreach ((new Finder)->in($config->getDirs())->files() as $file) {

            /** @var $file SplFileInfo: */

            try {
                $code = file_get_contents($file->getPathname());
                $stmts = $parser->parse($code);
                $stmts = $traverser->traverse($stmts);

            } catch (\PhpParser\Error $e) {
                $output->writeln(
                    '<error>Parse Error: ' . $file->getPathname() . ' - ' . $e->getMessage() . '</error>'
                );
            }
        }


        (new GraphDrawer())->draw($map);

        #var_dump($map->getDependencies());
    }

} 