<?php

namespace DEPTRAC_202402;

use DEPTRAC_202402\JetBrains\PhpStorm\Pure;
/**
 * @since 8.0
 */
class ReflectionUnionType extends \ReflectionType
{
    /**
     * Get list of named types of union type
     *
     * @return ReflectionNamedType[]
     */
    #[Pure]
    public function getTypes() : array
    {
    }
}
/**
 * @since 8.0
 */
\class_alias('DEPTRAC_202402\\ReflectionUnionType', 'ReflectionUnionType', \false);
