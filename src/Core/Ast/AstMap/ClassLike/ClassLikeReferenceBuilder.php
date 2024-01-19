<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
final class ClassLikeReferenceBuilder extends ReferenceBuilder
{
    /** @var AstInherit[] */
    private array $inherits = [];
    /**
     * @param list<string> $tokenTemplates
     */
    private function __construct(array $tokenTemplates, string $filepath, private readonly \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken $classLikeToken, private readonly \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType $classLikeType, private readonly bool $isInternal)
    {
        parent::__construct($tokenTemplates, $filepath);
    }
    /**
     * @param list<string> $classTemplates
     */
    public static function createClassLike(string $filepath, string $classLikeName, array $classTemplates, bool $isInternal) : self
    {
        return new self($classTemplates, $filepath, \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType::TYPE_CLASSLIKE, $isInternal);
    }
    /**
     * @param list<string> $classTemplates
     */
    public static function createClass(string $filepath, string $classLikeName, array $classTemplates, bool $isInternal) : self
    {
        return new self($classTemplates, $filepath, \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType::TYPE_CLASS, $isInternal);
    }
    /**
     * @param list<string> $classTemplates
     */
    public static function createTrait(string $filepath, string $classLikeName, array $classTemplates, bool $isInternal) : self
    {
        return new self($classTemplates, $filepath, \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType::TYPE_TRAIT, $isInternal);
    }
    /**
     * @param list<string> $classTemplates
     */
    public static function createInterface(string $filepath, string $classLikeName, array $classTemplates, bool $isInternal) : self
    {
        return new self($classTemplates, $filepath, \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType::TYPE_INTERFACE, $isInternal);
    }
    /** @internal */
    public function build() : \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference
    {
        return new \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference($this->classLikeToken, $this->classLikeType, $this->inherits, $this->dependencies, $this->isInternal);
    }
    public function extends(string $classLikeName, int $occursAtLine) : self
    {
        $this->inherits[] = new AstInherit(\Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), AstInheritType::EXTENDS);
        return $this;
    }
    public function implements(string $classLikeName, int $occursAtLine) : self
    {
        $this->inherits[] = new AstInherit(\Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), AstInheritType::IMPLEMENTS);
        return $this;
    }
    public function trait(string $classLikeName, int $occursAtLine) : self
    {
        $this->inherits[] = new AstInherit(\Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken::fromFQCN($classLikeName), new FileOccurrence($this->filepath, $occursAtLine), AstInheritType::USES);
        return $this;
    }
}
