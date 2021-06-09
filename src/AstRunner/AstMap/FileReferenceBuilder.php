<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

final class FileReferenceBuilder
{
    /** @var AstDependency[] */
    private $dependencies = [];

    /** @var string */
    private $filepath;

    /** @var ClassReferenceBuilder[] */
    private $classReferences;

    /** @var ClassReferenceBuilder|null */
    private $currentClassReference;

    /** @var bool */
    private $countUseStatementsAsDependencies;

    private function __construct(string $filepath, bool $countUseStatementsAsDependencies)
    {
        $this->filepath = $filepath;
        $this->classReferences = [];
        $this->countUseStatementsAsDependencies = $countUseStatementsAsDependencies;
    }

    public static function create(string $filepath, bool $countUseStatementsAsDependencies = true): self
    {
        return new self($filepath, $countUseStatementsAsDependencies);
    }

    public function use(string $classLikeName, int $occursAtLine): self
    {
        if ($this->countUseStatementsAsDependencies) {
            $this->dependencies[] = AstDependency::useStmt(
                ClassLikeName::fromFQCN($classLikeName),
                FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
            );
        }

        return $this;
    }

    public function newClassLike(string $classLikeName): ClassReferenceBuilder
    {
        $this->classReferences[] = $this->currentClassReference = ClassReferenceBuilder::create($this->filepath, $classLikeName);

        return $this->currentClassReference;
    }

    public function hasCurrentClassLike(): bool
    {
        return null !== $this->currentClassReference;
    }

    public function currentClassLike(): ClassReferenceBuilder
    {
        if (null === $this->currentClassReference) {
            throw new \RuntimeException('No class like has been defined before.');
        }

        return $this->currentClassReference;
    }

    public function build(): AstFileReference
    {
        $classReferences = [];
        foreach ($this->classReferences as $classReference) {
            $classReferences[] = $classReference->build();
        }

        return new AstFileReference($this->filepath, $this->dependencies, $classReferences);
    }
}
