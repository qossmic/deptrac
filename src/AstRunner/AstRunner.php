<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner;

use PhpParser\Error;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\AstRunner\AstParser\AstParser;
use Qossmic\Deptrac\Event\AstRunner\AstFileAnalysedEvent;
use Qossmic\Deptrac\Event\AstRunner\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Event\AstRunner\PostCreateAstMapEvent;
use Qossmic\Deptrac\Event\AstRunner\PreCreateAstMapEvent;

class AstRunner
{
    private EventDispatcherInterface $dispatcher;
    private AstParser $astParser;

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

                $this->dispatcher->dispatch(new AstFileAnalysedEvent($file));
            } catch (Error $e) {
                $this->dispatcher->dispatch(new AstFileSyntaxErrorEvent($file, $e->getMessage()));
            }
        }

        $astMap = new AstMap($references);
        $this->dispatcher->dispatch(new PostCreateAstMapEvent());

        return $astMap;
    }
}
