<?php 

namespace DependencyTracker;

class AstMap
{
    protected $fileAstMap = [];

    /**
     * AstMap constructor.
     * @param array $fileAstMap
     */
    public function __construct(array $fileAstMap)
    {
        $this->fileAstMap = $fileAstMap;
    }

}
