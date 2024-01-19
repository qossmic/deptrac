<?php

/*
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */
declare (strict_types=1);
namespace DEPTRAC_202401\phpDocumentor\Reflection\PseudoTypes;

use DEPTRAC_202401\phpDocumentor\Reflection\PseudoType;
use DEPTRAC_202401\phpDocumentor\Reflection\Type;
use DEPTRAC_202401\phpDocumentor\Reflection\Types\Float_;
/** @psalm-immutable */
class FloatValue implements PseudoType
{
    private float $value;
    public function __construct(float $value)
    {
        $this->value = $value;
    }
    public function getValue() : float
    {
        return $this->value;
    }
    public function underlyingType() : Type
    {
        return new Float_();
    }
    public function __toString() : string
    {
        return (string) $this->value;
    }
}
