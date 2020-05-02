<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

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

    private function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->classReferences = [];
    }

    public static function create(string $filepath): self
    {
        return new self($filepath);
    }

    public function use(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = AstDependency::useStmt(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );

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
