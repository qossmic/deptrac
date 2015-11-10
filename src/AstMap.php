<?php 

namespace DependencyTracker;

use DependencyTracker\AstMap\AstInherit;
use DependencyTracker\AstMap\FlattenAstInherit;
use DependencyTracker\DependencyResult\InheritDependency;

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

    /**
     * @param $classA
     * @param AstInherit[] $inheritClasses
     */
    public function setClassInherit($classA, array $inheritClasses)
    {
        if (empty($inheritClasses)) {
            return;
        }

        $this->classInheritMap[$classA] = $inheritClasses;
    }

    /**
     * @return array
     */
    public function getAllInherits()
    {
        return $this->classInheritMap;
    }

    /**
     * @param $classA
     * @return AstInherit[]
     */
    public function getClassInherits($classA)
    {
        if (!isset($this->classInheritMap[$classA])) {
            return [];
        }

        return $this->classInheritMap[$classA];
    }

    /**
     * @param $classA
     * @param FlattenAstInherit $inheritClasses
     */
    public function setFlattenClassInherit($classA, FlattenAstInherit $inheritClasses)
    {
        if (empty($inheritClasses)) {
            return;
        }

        $this->flattenClassInheritMap[$classA] = $inheritClasses;
    }

    /**
     * @return array
     */
    public function getAllFlattenClassInherits()
    {
        return $this->flattenClassInheritMap;
    }

    /**
     * @param $classA
     * @return FlattenAstInherit[]
     */
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
