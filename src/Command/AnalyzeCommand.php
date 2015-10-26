<?php

namespace DependencyTracker\Command;


use DependencyTracker\AstMap;
use DependencyTracker\ClassLayerMap;
use DependencyTracker\Collector\ClassNameCollector;
use DependencyTracker\Configuration;
use DependencyTracker\Event\AstFileAnalyzedEvent;
use DependencyTracker\Event\AstFileSyntaxErrorEvent;
use DependencyTracker\Event\PostCreateAstMapEvent;
use DependencyTracker\Event\PreCreateAstMapEvent;
use DependencyTracker\Formatter\ConsoleFormatter;
use DependencyTracker\OutputFormatter\GraphVizOutputFormatter;
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
            Yaml::parse(file_get_contents(getcwd().'/depfile.yml'))
        );

        new ConsoleFormatter($this->dispatcher, $output);


        // Step1
        $files = iterator_to_array(
            (new Finder)
                ->in($config->getPaths())
                ->name('*.php')
                ->files()
                ->followLinks()
                ->ignoreUnreadableDirs(true)
                ->ignoreVCS(true)
        );

        $files = array_filter($files, function(\SplFileInfo $fileInfo) use ($config) {
            foreach ($config->getExcludeFiles() as $excludeFiles) {
                if(preg_match('/'.$excludeFiles.'/i', $fileInfo->getPathname())) {
                    return false;
                }
            }

            return true;
        });

        $a = 0;

        $cacheKey = sha1(array_reduce(
            array_map(function(\SplFileInfo $fileInfo) {
                return md5_file($fileInfo->getPathname());
            }, $files),
            function($a, $b) { return $a + $b; }
        ));

        $cacheFile = sys_get_temp_dir().'/astmap.cache.'.$cacheKey;

        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(count($files)));

        if (file_exists($cacheFile)) {
            $output->writeln("reading cachefile <info>".$cacheFile."</info>");
            $astMap = unserialize(file_get_contents($cacheFile));
        } else {
            $output->writeln("writing cachefile <info>".$cacheFile."</info>");
            $this->createAstMapByFiles($astMap = new AstMap(), $files);
            file_put_contents($cacheFile, serialize($astMap));
        }

        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($astMap));


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