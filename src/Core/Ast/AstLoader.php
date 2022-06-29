<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast;

use PhpParser\Error;
use Qossmic\Deptrac\Contract\Ast\AstFileAnalysedEvent;
use Qossmic\Deptrac\Contract\Ast\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Contract\Ast\PostCreateAstMapEvent;
use Qossmic\Deptrac\Contract\Ast\PreCreateAstMapEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
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
