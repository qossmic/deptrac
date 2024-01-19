<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace DEPTRAC_202401\phpDocumentor\Reflection\PseudoTypes;

use DEPTRAC_202401\phpDocumentor\Reflection\PseudoType;
use DEPTRAC_202401\phpDocumentor\Reflection\Type;
use DEPTRAC_202401\phpDocumentor\Reflection\Types\String_;
/**
 * Value Object representing the type 'string'.
 *
 * @psalm-immutable
 */
final class NonEmptyString extends String_ implements PseudoType
{
    public function underlyingType() : Type
    {
        return new String_();
    }
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'non-empty-string';
    }
}
