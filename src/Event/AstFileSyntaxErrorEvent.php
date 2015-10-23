<?php

namespace DependencyTracker\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\SplFileInfo;

class AstFileSyntaxErrorEvent extends Event
{
    protected $file;

    protected $syntaxError;

    /**
     * AstFileSyntaxErrorEvent constructor.
     * @param $filepath
     * @param $syntaxError
     */
    public function __construct(SplFileInfo $file, $syntaxError)
    {
        $this->file = $file;
        $this->syntaxError = $syntaxError;
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
    public function getSyntaxError()
    {
        return $this->syntaxError;
    }

}
