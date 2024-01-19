<?php

namespace DEPTRAC_202401;

use DEPTRAC_202401\JetBrains\PhpStorm\Internal\TentativeType;
use DEPTRAC_202401\JetBrains\PhpStorm\Pure;
/**
 * @since 7.1
 */
class ReflectionNamedType extends \ReflectionType
{
    /**
     * Get the text of the type hint.
     *
     * @link https://php.net/manual/en/reflectionnamedtype.getname.php
     * @return string Returns the text of the type hint.
     * @since 7.1
     */
    #[Pure]
    public function getName()
    {
    }
    /**
     * Checks if it is a built-in type
     *
     * @link https://php.net/manual/en/reflectionnamedtype.isbuiltin.php
     * @return bool Returns {@see true} if it's a built-in type, otherwise {@see false}
     *
     * @since 7.1 overrides the parent {@see ReflectionType::isBuiltin()} method.
     * @since 8.0 method was removed from the parent {@see ReflectionType} class.
     */
    #[Pure]
    #[TentativeType]
    public function isBuiltin() : bool
    {
    }
}
/**
 * @since 7.1
 */
\class_alias('DEPTRAC_202401\\ReflectionNamedType', 'ReflectionNamedType', \false);
