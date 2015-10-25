<?php 

namespace DependencyTracker;

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

    public function getAstByFilePathname($filePathname)
    {
        if (!isset($this->fileAstMap[$filePathname])) {
            return null;
        }

        return $this->fileAstMap[$filePathname];
    }

}
