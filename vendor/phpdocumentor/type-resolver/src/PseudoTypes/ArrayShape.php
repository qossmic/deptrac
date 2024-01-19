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
use DEPTRAC_202401\phpDocumentor\Reflection\Types\Array_;
use DEPTRAC_202401\phpDocumentor\Reflection\Types\ArrayKey;
use DEPTRAC_202401\phpDocumentor\Reflection\Types\Mixed_;
use function implode;
/** @psalm-immutable */
class ArrayShape implements PseudoType
{
    /** @var ArrayShapeItem[] */
    private array $items;
    public function __construct(ArrayShapeItem ...$items)
    {
        $this->items = $items;
    }
    /**
     * @return ArrayShapeItem[]
     */
    public function getItems() : array
    {
        return $this->items;
    }
    public function underlyingType() : Type
    {
        return new Array_(new Mixed_(), new ArrayKey());
    }
    public function __toString() : string
    {
        return 'array{' . implode(', ', $this->items) . '}';
    }
}
