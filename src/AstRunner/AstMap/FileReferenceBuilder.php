<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

final class FileReferenceBuilder
{
    /** 
     * @var AstDependency[] 
     * @deprecated 
     */
    private array $dependencies = [];

    private string $filepath;

    /** @var ClassReferenceBuilder[] */
    private array $classReferences = [];

    private ?ClassReferenceBuilder $currentClassReference = null;

    /**
     * @deprecated use `UseVisitor` instead
     */
    private bool $countUseStatementsAsDependencies;

    private function __construct(string $filepath, bool $countUseStatementsAsDependencies)
    {
        $this->filepath = $filepath;
        $this->countUseStatementsAsDependencies = $countUseStatementsAsDependencies;
    }

    public static function create(string $filepath, bool $countUseStatementsAsDependencies = true): self
    {
        return new self($filepath, $countUseStatementsAsDependencies);
    }

    /**
     * @deprecated use `UseVisitor` instead
     */
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

        return new AstFileReference($this->filepath, $classReferences, $this->dependencies);
    }
}
