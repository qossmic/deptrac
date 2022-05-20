<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Ast\AstMap\ReferenceBuilder;

final class ClassLikeReferenceBuilder extends ReferenceBuilder
{
    private ClassLikeToken $classLikeToken;
    private ClassLikeType $classLikeType;

    /** @var AstInherit[] */
    private array $inherits = [];

    /**
     * @param string[] $tokenTemplates
     */
    protected function __construct(array $tokenTemplates, string $filepath, ClassLikeToken $classLikeToken, ClassLikeType $classLikeType)
    {
        parent::__construct($tokenTemplates, $filepath);

        $this->classLikeToken = $classLikeToken;
        $this->classLikeType = $classLikeType;
    }

    /**
     * @param string[] $classTemplates
     */
    public static function createClassLike(string $filepath, string $classLikeName, array $classTemplates): self
    {
        return new self($classTemplates, $filepath, ClassLikeToken::fromFQCN($classLikeName), ClassLikeType::classLike());
    }

    /**
     * @param string[] $classTemplates
     */
    public static function createClass(string $filepath, string $classLikeName, array $classTemplates): self
    {
        return new self($classTemplates, $filepath, ClassLikeToken::fromFQCN($classLikeName), ClassLikeType::class());
    }

    /**
     * @param string[] $classTemplates
     */
    public static function createTrait(string $filepath, string $classLikeName, array $classTemplates): self
    {
        return new self($classTemplates, $filepath, ClassLikeToken::fromFQCN($classLikeName), ClassLikeType::trait());
    }

    /**
     * @param string[] $classTemplates
     */
    public static function createInterface(string $filepath, string $classLikeName, array $classTemplates): self
    {
        return new self($classTemplates, $filepath, ClassLikeToken::fromFQCN($classLikeName), ClassLikeType::interface());
    }

    /** @internal */
    public function build(): ClassLikeReference
    {
        return new ClassLikeReference(
            $this->classLikeToken,
            $this->classLikeType,
            $this->inherits,
            $this->dependencies
        );
    }

    public function extends(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newExtends(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }

    public function implements(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newImplements(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }

    public function trait(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newTraitUse(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }
}
