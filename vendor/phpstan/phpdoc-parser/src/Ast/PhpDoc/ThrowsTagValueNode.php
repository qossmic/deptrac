<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PHPStan\PhpDocParser\Ast\PhpDoc;

use DEPTRAC_202401\PHPStan\PhpDocParser\Ast\NodeAttributes;
use DEPTRAC_202401\PHPStan\PhpDocParser\Ast\Type\TypeNode;
use function trim;
class ThrowsTagValueNode implements PhpDocTagValueNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    /** @var string (may be empty) */
    public $description;
    public function __construct(TypeNode $type, string $description)
    {
        $this->type = $type;
        $this->description = $description;
    }
    public function __toString() : string
    {
        return trim("{$this->type} {$this->description}");
    }
}
