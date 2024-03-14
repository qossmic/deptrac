<?php

namespace DEPTRAC_202403;

use DEPTRAC_202403\JetBrains\PhpStorm\Pure;
/**
 * @link https://php.net/manual/en/class.reflectionenumunitcase.php
 * @since 8.1
 */
class ReflectionEnumUnitCase extends \ReflectionClassConstant
{
    public function __construct(object|string $class, string $constant)
    {
    }
    #[Pure]
    public function getValue() : \UnitEnum
    {
    }
    /**
     * @return ReflectionEnum
     */
    #[Pure]
    public function getEnum() : \ReflectionEnum
    {
    }
}
/**
 * @link https://php.net/manual/en/class.reflectionenumunitcase.php
 * @since 8.1
 */
\class_alias('DEPTRAC_202403\\ReflectionEnumUnitCase', 'ReflectionEnumUnitCase', \false);
