<?php

namespace DEPTRAC_202401\JetBrains\PhpStorm\Internal;

use Attribute;
use DEPTRAC_202401\JetBrains\PhpStorm\Deprecated;
use DEPTRAC_202401\JetBrains\PhpStorm\ExpectedValues;
/**
 * For PhpStorm internal use only
 * @since 8.0
 * @internal
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
class PhpStormStubsElementAvailable
{
    public function __construct(#[ExpectedValues(Deprecated::PHP_VERSIONS)] $from, #[ExpectedValues(Deprecated::PHP_VERSIONS)] $to = null)
    {
    }
}
