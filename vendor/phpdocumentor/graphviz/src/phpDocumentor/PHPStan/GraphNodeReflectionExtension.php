<?php

declare (strict_types=1);
/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace DEPTRAC_202401\phpDocumentor\GraphViz\PHPStan;

use DEPTRAC_202401\phpDocumentor\GraphViz\Graph;
use DEPTRAC_202401\phpDocumentor\GraphViz\Node;
use DEPTRAC_202401\PHPStan\Reflection\Annotations\AnnotationPropertyReflection;
use DEPTRAC_202401\PHPStan\Reflection\ClassReflection;
use DEPTRAC_202401\PHPStan\Reflection\PropertiesClassReflectionExtension;
use DEPTRAC_202401\PHPStan\Reflection\PropertyReflection;
use DEPTRAC_202401\PHPStan\Type\ObjectType;
final class GraphNodeReflectionExtension implements PropertiesClassReflectionExtension
{
    public function hasProperty(ClassReflection $classReflection, string $propertyName) : bool
    {
        return $classReflection->getName() === Graph::class;
    }
    public function getProperty(ClassReflection $classReflection, string $propertyName) : PropertyReflection
    {
        return new AnnotationPropertyReflection($classReflection, new ObjectType(Node::class), \true, \true);
    }
}
