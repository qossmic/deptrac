<?php

namespace DependencyTracker;

use DependencyTracker\AstMap\AstInherit;
use DependencyTracker\AstMap\FlattenAstInherit;
use DependencyTracker\AstMap\InheritDependency;
use DependencyTracker\Event\AstFileAnalyzedEvent;
use DependencyTracker\Event\AstFileSyntaxErrorEvent;
use DependencyTracker\Event\PostCreateAstMapEvent;
use DependencyTracker\Event\PreCreateAstMapEvent;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceB;
use PhpParser\Node\Name;
use PhpParser\NodeVisitor\NameResolver;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AstMapGenerator
{
    protected $dispatcher;

    public function __construct(
        EventDispatcherInterface $dispatcher
    )
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Configuration $configuration
     * @param OutputInterface $output
     * @return AstMap|mixed
     */
    public function generateAstMap(Configuration $configuration, OutputInterface $output)
    {
        $files = $this->collectFiles($configuration);

        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(count($files)));

        $this->createAstMapByFiles($astMap = new AstMap(), $files);

        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($astMap));

        return $astMap;
    }

    private function collectFiles(Configuration $configuration)
    {
        $files = iterator_to_array(
            (new Finder)
                ->in($configuration->getPaths())
                ->name('*.php')
                ->files()
                ->followLinks()
                ->ignoreUnreadableDirs(true)
                ->ignoreVCS(true)
        );

        return array_filter($files, function(\SplFileInfo $fileInfo) use ($configuration) {
            foreach ($configuration->getExcludeFiles() as $excludeFiles) {
                if(preg_match('/'.$excludeFiles.'/i', $fileInfo->getPathname())) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * @param AstMap $astMap
     * @param SplFileInfo[] $files
     */
    public function createAstMapByFiles(AstMap $astMap, array $files)
    {
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);
        $traverser = new \PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver());

        gc_disable();

        foreach ($files as $file) {

            try {
                $code = file_get_contents($file->getPathname());
                $astMap->add($file->getPathname(), $ast = $traverser->traverse($parser->parse($code)));

                // add basic inheritance informations for every class.
                foreach (AstHelper::findClassLikeNodes($ast) as $classLikeNodes) {
                    $astMap->setClassInherit(
                        $classLikeNodes->namespacedName->toString(),
                        AstHelper::findInheritances($classLikeNodes)
                    );
                }

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

        gc_enable();

        $this->flattenInheritanceDependencies($astMap);
    }

    private function flattenInheritanceDependencies(AstMap $astMap)
    {

        foreach ($astMap->getAllInherits() as $klass => $inherits) {

            $inerhitInerhits = [];

            foreach ($inherits as $inherit) {

                // todo, dass ist falsch, jeder pfad muss zu einem FlattenAstInherit werden!
                $inerhitInerhits =  new FlattenAstInherit($this->resolveDepsRecursive($inherit, $astMap));
            }

            $astMap->setFlattenClassInherit(
                $klass,
                $inerhitInerhits
            );
        }
    }

    private function resolveDepsRecursive(AstInherit $inheritDependency, AstMap $astMap, \ArrayObject $alreadyResolved = null)
    {
        if ($alreadyResolved == null) {
            $alreadyResolved = new \ArrayObject();
        }

        // recursion detected
        if (isset($alreadyResolved[$inheritDependency->getClassName()])) {
            return [];
        }

        $alreadyResolved[$inheritDependency->getClassName()] = true;

        $buffer = [];
        foreach ($astMap->getClassInherits($inheritDependency->getClassName()) as $dep) {
            $buffer = array_merge($buffer, $this->resolveDepsRecursive($dep, $astMap, $alreadyResolved));
            $buffer[] = $dep;
        }

        return array_values(array_unique($buffer));
    }


}
