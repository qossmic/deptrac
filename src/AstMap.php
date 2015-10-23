<?php 

namespace DependencyTracker;

use DependencyTracker\AstHint\AstHintInterface;

class AstMap
{
    protected $fileAstMap;

    /**
     * AstMap constructor.
     * @param array $fileAstMap
     */
    public function __construct(array $fileAstMap = [])
    {
        $this->fileAstMap = $fileAstMap;
    }

    public function add($filePathname, $ast)
    {
        $this->fileAstMap[$filePathname] = $ast;
    }

    /**
     * @return array
     */
    public function getAsts()
    {
        return $this->fileAstMap;
    }

}
