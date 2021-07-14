<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap\File;

use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionToken\FunctionReferenceBuilder;

final class FileReferenceBuilder
{
    /** @var AstDependency[] */
    private array $directDependencies = [];

    private string $filepath;

    /** @var ClassReferenceBuilder[] */
    private array $classReferences = [];

    /** @var FunctionReferenceBuilder[] */
    private array $functionReferences = [];

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
        $this->directDependencies[] = AstDependency::fromType(
            ClassLikeName::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            AstDependency::USE
        );

        return $this;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newClassLike(string $classLikeName, array $templateTypes = []): ClassReferenceBuilder
    {
        $classReference = ClassReferenceBuilder::create($this->filepath, $classLikeName, $templateTypes);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    public function newFunction(string $functionName): FunctionReferenceBuilder
    {
        $functionReference = FunctionReferenceBuilder::create($this->filepath, $functionName);
        $this->functionReferences[] = $functionReference;

        return $functionReference;
    }

    public function build(): AstFileReference
    {
        $classReferences = [];
        foreach ($this->classReferences as $classReference) {
            $classReferences[] = $classReference->build();
        }

        $functionReferences = [];
        foreach ($this->functionReferences as $functionReference) {
            $functionReferences[] = $functionReference->build();
        }

        return new AstFileReference($this->filepath, $classReferences, $functionReferences, $this->directDependencies);
    }
}
