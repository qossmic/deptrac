<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node;

use DEPTRAC_202401\PhpParser\NodeAbstract;
/**
 * Represents the "..." in "foo(...)" of the first-class callable syntax.
 */
class VariadicPlaceholder extends NodeAbstract
{
    /**
     * Create a variadic argument placeholder (first-class callable syntax).
     *
     * @param array $attributes Additional attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
    public function getType() : string
    {
        return 'VariadicPlaceholder';
    }
    public function getSubNodeNames() : array
    {
        return [];
    }
}
