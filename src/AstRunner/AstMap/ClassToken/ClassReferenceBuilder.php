<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap\ClassToken;

use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenReferenceBuilder;

final class ClassReferenceBuilder extends TokenReferenceBuilder
{
    private string $classLikeName;

    /** @var AstInherit[] */
    private array $inherits = [];

    /**
     * @param string[] $tokenTemplates
     */
    protected function __construct(array $tokenTemplates, string $filepath, string $classLikeName)
    {
        parent::__construct($tokenTemplates, $filepath);
        $this->classLikeName = $classLikeName;
    }

    /**
     * @param string[] $classTemplates
     */
    public static function create(string $filepath, string $classLikeName, array $classTemplates): self
    {
        return new self($classTemplates, $filepath, $classLikeName);
    }

    /** @internal */
    public function build(): AstClassReference
    {
        return new AstClassReference(
            ClassLikeName::fromFQCN($this->classLikeName),
            $this->inherits,
            $this->dependencies
        );
    }

    public function extends(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newExtends(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }

    public function implements(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newImplements(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }

    public function trait(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newTraitUse(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

        return $this;
    }

    public function instanceof(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::INSTANCEOF
        );

        return $this;
    }

    public function newStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::NEW
        );

        return $this;
    }

    public function staticProperty(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::STATIC_PROPERTY
        );

        return $this;
    }

    public function staticMethod(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::STATIC_METHOD
        );

        return $this;
    }

    public function catchStmt(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::CATCH
        );

        return $this;
    }

    public function attribute(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::ATTRIBUTE
        );

        return $this;
    }

}
