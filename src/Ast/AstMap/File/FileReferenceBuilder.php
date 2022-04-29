<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\File;

use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeReferenceBuilder;
use Qossmic\Deptrac\Ast\AstMap\ReferenceBuilder;

final class FileReferenceBuilder extends ReferenceBuilder
{
    /** @var ClassLikeReferenceBuilder[] */
    private array $classReferences = [];

    /** @var FunctionLikeReferenceBuilder[] */
    private array $functionReferences = [];

    public static function create(string $filepath): self
    {
        return new self([], $filepath);
    }

    public function useStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = DependencyToken::fromType(
            ClassLikeToken::fromFQCN($classLikeName),
            FileOccurrence::fromFilepath($this->filepath, $occursAtLine),
            DependencyToken::USE
        );

        return $this;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newClassLike(string $classLikeName, array $templateTypes = []): ClassLikeReferenceBuilder
    {
        $classReference = ClassLikeReferenceBuilder::create($this->filepath, $classLikeName, $templateTypes);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newFunction(string $functionName, array $templateTypes = []): FunctionLikeReferenceBuilder
    {
        $functionReference = FunctionLikeReferenceBuilder::create($this->filepath, $functionName, $templateTypes);
        $this->functionReferences[] = $functionReference;

        return $functionReference;
    }

    public function build(): FileReference
    {
        $classReferences = [];
        foreach ($this->classReferences as $classReference) {
            $classReferences[] = $classReference->build();
        }

        $functionReferences = [];
        foreach ($this->functionReferences as $functionReference) {
            $functionReferences[] = $functionReference->build();
        }

        return new FileReference($this->filepath, $classReferences, $functionReferences, $this->dependencies);
    }
}
