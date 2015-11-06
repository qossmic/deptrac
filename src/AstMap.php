<?php 

namespace DependencyTracker;

class AstMap
{
    protected $fileAstMap;

    private $classInheritMap = [];

    private $flattenClassInheritMap = [];

    /**
     * AstMap constructor.
     * @param array $fileAstMap
     */
    public function __construct(array $fileAstMap = [])
    {
        $this->fileAstMap = $fileAstMap;
    }

    public function setClassInherit($classA, array $inheritClasses)
    {
        if (empty($inheritClasses)) {
            return;
        }

        $this->classInheritMap[$classA] =$inheritClasses;
    }

    public function getAllInherits()
    {
        return $this->classInheritMap;
    }

    public function getClassInherits($classA)
    {
        if (!isset($this->classInheritMap[$classA])) {
            return [];
        }

        return $this->classInheritMap[$classA];
    }

    public function setFlattenClassInherit($classA, array $inheritClasses)
    {
        if (empty($inheritClasses)) {
            return;
        }

        $this->flattenClassInheritMap[$classA] = $inheritClasses;
    }

    public function getAllFlattenClassInherits()
    {
        return $this->flattenClassInheritMap;
    }

    public function getFlattenClassInherits($classA)
    {
        if (!isset($this->flattenClassInheritMap[$classA])) {
            return [];
        }

        return $this->flattenClassInheritMap[$classA];
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
