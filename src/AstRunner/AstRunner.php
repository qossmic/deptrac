<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner;

use SensioLabs\Deptrac\AstRunner\AstParser\AstParser;
use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
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
     * @param \SplFileInfo[] $files
     */
    public function createAstMapByFiles(array $files): AstMap
    {
        $this->dispatcher->dispatch(new PreCreateAstMapEvent(count($files)));

        $astMap = new AstMap();

        foreach ($files as $file) {
            if (!$this->astParser->supports($file)) {
                continue;
            }

            try {
                $astMap->addAstFileReference($this->astParser->parse($file));

                $this->dispatcher->dispatch(new AstFileAnalyzedEvent($file));
            } catch (\PhpParser\Error $e) {
                $this->dispatcher->dispatch(new AstFileSyntaxErrorEvent($file, $e->getMessage()));
            }
        }

        $this->dispatcher->dispatch(new PostCreateAstMapEvent());

        return $astMap;
    }
}
