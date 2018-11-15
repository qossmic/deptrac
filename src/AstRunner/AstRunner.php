<?php

namespace SensioLabs\Deptrac\AstRunner;

use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AstRunner
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \SplFileInfo[] $files
     */
    public function createAstMapByFiles(AstParserInterface $astParser, array $files): AstMap
    {
        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(count($files)));

        $astMap = new AstMap($astParser);

        foreach ($files as $file) {
            try {
                $astMap->addAstFileReference($astParser->parse($file));

                $this->dispatcher->dispatch(AstFileAnalyzedEvent::class, new AstFileAnalyzedEvent($file));
            } catch (\PhpParser\Error $e) {
                $this->dispatcher->dispatch(
                    AstFileSyntaxErrorEvent::class,
                    new AstFileSyntaxErrorEvent($file, $e->getMessage())
                );
            }
        }

        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($astMap));

        return $astMap;
    }
}
