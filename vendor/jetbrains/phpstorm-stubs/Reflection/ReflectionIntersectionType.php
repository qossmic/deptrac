<?php

namespace DEPTRAC_202401;

use DEPTRAC_202401\JetBrains\PhpStorm\Pure;
/**
 * @since 8.1
 */
class ReflectionIntersectionType extends \ReflectionType
{
    /** @return ReflectionType[] */
    #[Pure]
    public function getTypes() : array
    {
    }
}
/**
 * @since 8.1
 */
\class_alias('DEPTRAC_202401\\ReflectionIntersectionType', 'ReflectionIntersectionType', \false);
