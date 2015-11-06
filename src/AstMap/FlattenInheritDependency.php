<?php 

namespace DependencyTracker\AstMap;

class FlattenInheritDependency
{
    /** @var InheritDependency[] */
    protected $inheritDependencies;

    public function __construct(array $inheritDependencies)
    {
        $this->inheritDependencies = $inheritDependencies;
    }

    public function __toString()
    {
        $buffer = '';
        foreach ($this->inheritDependencies as $v) {
            $buffer .= "{$v->__toString()} ";
        }
        return $buffer;
    }

    /** @return InheritDependency */
    public function first()
    {
        return $this->inheritDependencies[0];
    }

    /** @return InheritDependency */
    public function last()
    {
        return $this->inheritDependencies[count($this->inheritDependencies) - 1];
    }

}
