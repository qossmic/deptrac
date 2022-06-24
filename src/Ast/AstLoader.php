<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast;

use PhpParser\Error;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Events\Ast\AstFileAnalysedEvent;
use Qossmic\Deptrac\Events\Ast\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Events\Ast\PostCreateAstMapEvent;
use Qossmic\Deptrac\Events\Ast\PreCreateAstMapEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AstLoader
{
    private ParserInterface $parser;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ParserInterface $parser,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->parser = $parser;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string[] $files
     */
    public function createAstMap(array $files): AstMap
    {
        $references = [];

        $this->eventDispatcher->dispatch(new PreCreateAstMapEvent(count($files)));

        foreach ($files as $file) {
            try {
                $references[] = $this->parser->parseFile($file);

                $this->eventDispatcher->dispatch(new AstFileAnalysedEvent($file));
            } catch (Error $e) {
                $this->eventDispatcher->dispatch(new AstFileSyntaxErrorEvent($file, $e->getMessage()));
            }
        }

        $astMap = new AstMap($references);
        $this->eventDispatcher->dispatch(new PostCreateAstMapEvent());

        return $astMap;
    }
}
