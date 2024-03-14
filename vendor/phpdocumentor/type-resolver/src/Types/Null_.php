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
namespace DEPTRAC_202403\phpDocumentor\Reflection\Types;

use DEPTRAC_202403\phpDocumentor\Reflection\Type;
/**
 * Value Object representing a null value or type.
 *
 * @psalm-immutable
 */
final class Null_ implements Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'null';
    }
}
