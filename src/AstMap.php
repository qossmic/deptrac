<?php 

namespace DependencyTracker;

use DependencyTracker\AstHint\AstHintInterface;

class AstMap
{
    protected $fileAstMap = [];

    protected $fileAstHint = [];

    /**
     * AstMap constructor.
     * @param array $fileAstMap
     */
    public function __construct(array $fileAstMap)
    {
        $this->fileAstMap = $fileAstMap;
    }

    /**
     * @return array
     */
    public function getAsts()
    {
        return $this->fileAstMap;
    }

    public function addHintForFile($filePath, AstHintInterface $astHint)
    {
        if (!isset($this->fileAstHint[$filePath])) {
            $this->fileAstHint[$filePath] = [];
        }

        $this->fileAstHint[$filePath][] = $astHint;
    }

    /**
     * @param $filePath
     * @param null $filterByClass
     * @return AstHintInterface{}
     */
    public function getAstHintsForFile($filePath, $filterByClass = null)
    {
        if (!isset($this->fileAstHint[$filePath])) {
            return [];
        }

        if (!$filterByClass) {
            return $this->fileAstHint[$filePath];
        }

        return array_filter($this->fileAstHint[$filePath], function($v) use ($filterByClass) {
            return is_a($v, $filterByClass, true);
        });
    }

}
