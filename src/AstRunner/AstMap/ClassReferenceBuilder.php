<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

final class ClassReferenceBuilder
{
    /** @var AstFileReference */
    private $fileReference;

    /** @var string */
    private $classLikeName;

    /** @var AstInherit[] */
    private $inherits = [];

    /** @var AstDependency[] */
    private $dependencies = [];

    private function __construct(AstFileReference $fileReference, string $classLikeName)
    {
        $this->fileReference = $fileReference;
        $this->classLikeName = $classLikeName;
    }

    public static function create(AstFileReference $fileReference, string $classLikeName): self
    {
        return new static($fileReference, $classLikeName);
    }

    public function build(): AstClassReference
    {
        return $this->fileReference->addClassReference(
            ClassLikeName::fromFQCN($this->classLikeName),
            $this->inherits,
            $this->dependencies
        );
    }

    public function extends(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newExtends(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function implements(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newImplements(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function trait(string $classLikeName, int $occursAtLine): self
    {
        $this->inherits[] = AstInherit::newTraitUse(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function instanceof(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::instanceofExpr(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function parameter(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::parameter(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function newStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::newStmt(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function staticProperty(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::staticProperty(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function staticMethod(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::staticMethod(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function returnType(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::returnType(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function catchStmt(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::catchStmt(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function variable(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::variable(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function throwStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::throwStmt(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function anonymousClassExtends(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::anonymousClassExtends(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function anonymousClassImplements(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::anonymousClassImplements(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }

    public function constFetch(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::constFetch(
            ClassLikeName::fromFQCN($classLikeName),
            new FileOccurrence($this->fileReference, $occursAtLine)
        );

        return $this;
    }
}
