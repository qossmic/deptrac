<?php

namespace DEPTRAC_202401;

use DEPTRAC_202401\JetBrains\PhpStorm\Deprecated;
use DEPTRAC_202401\JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use DEPTRAC_202401\JetBrains\PhpStorm\Internal\TentativeType;
/**
 * The reflection class.
 *
 * @link https://php.net/manual/en/class.reflection.php
 */
class Reflection
{
    /**
     * Gets modifier names
     *
     * @link https://php.net/manual/en/reflection.getmodifiernames.php
     * @param int $modifiers Bitfield of the modifiers to get.
     * @return string[] An array of modifier names.
     */
    #[TentativeType]
    public static function getModifierNames(#[LanguageLevelTypeAware(['8.0' => 'int'], default: '')] $modifiers) : array
    {
    }
    /**
     * Exports
     *
     * @link https://php.net/manual/en/reflection.export.php
     * @param Reflector $reflector The reflection to export.
     * @param bool $return Setting to {@see true} will return the export, as
     * opposed to emitting it. Setting to {@see false} (the default) will do the opposite.
     * @return string|null If the return parameter is set to {@see true}, then the
     * export is returned as a string, otherwise {@see null} is returned.
     * @removed 8.0
     */
    #[Deprecated(since: '7.4')]
    public static function export(\Reflector $reflector, $return = \false)
    {
    }
}
/**
 * The reflection class.
 *
 * @link https://php.net/manual/en/class.reflection.php
 */
\class_alias('DEPTRAC_202401\\Reflection', 'Reflection', \false);
