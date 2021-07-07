<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

final class FileReferenceBuilder
{
    /** @var AstDependency[] */
    private array $useStatements = [];

    private string $filepath;

    /** @var ClassReferenceBuilder[] */
    private array $classReferences = [];

    private ?ClassReferenceBuilder $currentClassReference = null;

    /**
     * @deprecated use `UseVisitor` instead
     */
    private bool $countUseStatementsAsDependencies;

    private function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public static function create(string $filepath): self
    {
        return new self($filepath);
    }

    public function newUseStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->useStatements[] = AstDependency::useStmt(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine)
        );
        return $this;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newClassLike(string $classLikeName, array $templateTypes = []): ClassReferenceBuilder
    {
        $this->classReferences[] = $this->currentClassReference = ClassReferenceBuilder::create($this->filepath, $classLikeName, $templateTypes);

        return $this->currentClassReference;
    }

    public function currentClassLike(): ?ClassReferenceBuilder
    {
        return $this->currentClassReference;
    }

    public function build(): AstFileReference
    {
        $classReferences = [];
        foreach ($this->classReferences as $classReference) {
            $classReferences[] = $classReference->build();
        }

        return new AstFileReference($this->filepath, $classReferences, $this->useStatements);
    }
}
