<?php

namespace DependencyTracker\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\SplFileInfo;

class AstFileAnalyzedEvent extends Event
{
    protected $file;

    protected $ast;

    /**
     * AstFileAnalyzedEvent constructor.
     * @param $filepath
     * @param $ast
     */
    public function __construct(SplFileInfo $file, $ast)
    {
        $this->file = $file;
        $this->ast = $ast;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getAst()
    {
        return $this->ast;
    }

}
