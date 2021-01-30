<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner;

use Qossmic\Deptrac\AstRunner\AstParser\AstParser;
use Qossmic\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use Qossmic\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AstRunner
{
    private $dispatcher;
    private $astParser;

    public function __construct(EventDispatcherInterface $dispatcher, AstParser $astParser)
    {
        $this->dispatcher = $dispatcher;
        $this->astParser = $astParser;
    }

    /**
     * @param string[] $files
     */
    public function createAstMapByFiles(array $files): AstMap
    {
        $references = [];

        $this->dispatcher->dispatch(new PreCreateAstMapEvent(count($files)));

        foreach ($files as $file) {
            try {
                $references[] = $this->astParser->parseFile($file);

                $this->dispatcher->dispatch(new AstFileAnalyzedEvent($file));
            } catch (\PhpParser\Error $e) {
                $this->dispatcher->dispatch(new AstFileSyntaxErrorEvent($file, $e->getMessage()));
            }
        }

        $astMap = new AstMap($references);
        $this->dispatcher->dispatch(new PostCreateAstMapEvent());

        return $astMap;
    }
}
