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

use DEPTRAC_202401\phpDocumentor\GraphViz\Attribute;
use DEPTRAC_202401\phpDocumentor\GraphViz\AttributeNotFound;
use DEPTRAC_202401\PHPStan\Reflection\ClassMemberReflection;
use DEPTRAC_202401\PHPStan\Reflection\ClassReflection;
use DEPTRAC_202401\PHPStan\Reflection\FunctionVariant;
use DEPTRAC_202401\PHPStan\Reflection\MethodReflection;
use DEPTRAC_202401\PHPStan\Reflection\ParametersAcceptor;
use DEPTRAC_202401\PHPStan\TrinaryLogic;
use DEPTRAC_202401\PHPStan\Type\Generic\TemplateTypeMap;
use DEPTRAC_202401\PHPStan\Type\ObjectType;
use DEPTRAC_202401\PHPStan\Type\Type;
final class AttributeGetterMethodReflection implements MethodReflection
{
    /** @var ClassReflection */
    private $classReflection;
    /** @var string */
    private $name;
    public function __construct(ClassReflection $classReflection, string $name)
    {
        $this->classReflection = $classReflection;
        $this->name = $name;
    }
    public function getDeclaringClass() : ClassReflection
    {
        return $this->classReflection;
    }
    public function isStatic() : bool
    {
        return \false;
    }
    public function isPrivate() : bool
    {
        return \false;
    }
    public function isPublic() : bool
    {
        return \true;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getPrototype() : ClassMemberReflection
    {
        return $this;
    }
    /**
     * @return ParametersAcceptor[]
     */
    public function getVariants() : array
    {
        return [new FunctionVariant(TemplateTypeMap::createEmpty(), null, [], \false, new ObjectType(Attribute::class))];
    }
    public function getDocComment() : ?string
    {
        return null;
    }
    public function isDeprecated() : TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }
    public function getDeprecatedDescription() : ?string
    {
        return null;
    }
    public function isFinal() : TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }
    public function isInternal() : TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }
    public function getThrowType() : ?Type
    {
        return new ObjectType(AttributeNotFound::class);
    }
    public function hasSideEffects() : TrinaryLogic
    {
        return TrinaryLogic::createMaybe();
    }
}
