<?php

namespace DEPTRAC_202402;

use DEPTRAC_202402\JetBrains\PhpStorm\Pure;
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
\class_alias('DEPTRAC_202402\\ReflectionIntersectionType', 'ReflectionIntersectionType', \false);
