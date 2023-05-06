<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\File;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;

final class FileReferenceBuilder extends ReferenceBuilder
{
    /** @var ClassLikeReferenceBuilder[] */
    private array $classReferences = [];

    /** @var FunctionReferenceBuilder[] */
    private array $functionReferences = [];

    public static function create(string $filepath): self
    {
        return new self([], $filepath);
    }

    public function useStatement(string $classLikeName, int $occursAtLine): self
    {
        $this->dependencies[] = new DependencyToken(
            ClassLikeToken::fromFQCN($classLikeName),
            new FileOccurrence($this->filepath, $occursAtLine),
            DependencyType::USE
        );

        return $this;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newClass(string $classLikeName, array $templateTypes, bool $isInternal): ClassLikeReferenceBuilder
    {
        $classReference = ClassLikeReferenceBuilder::createClass($this->filepath, $classLikeName, $templateTypes, $isInternal);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newTrait(string $classLikeName, array $templateTypes, bool $isInternal): ClassLikeReferenceBuilder
    {
        $classReference = ClassLikeReferenceBuilder::createTrait($this->filepath, $classLikeName, $templateTypes, $isInternal);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newClassLike(string $classLikeName, array $templateTypes, bool $isInternal): ClassLikeReferenceBuilder
    {
        $classReference = ClassLikeReferenceBuilder::createClassLike($this->filepath, $classLikeName, $templateTypes, $isInternal);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newInterface(string $classLikeName, array $templateTypes, bool $isInternal): ClassLikeReferenceBuilder
    {
        $classReference = ClassLikeReferenceBuilder::createInterface($this->filepath, $classLikeName, $templateTypes, $isInternal);
        $this->classReferences[] = $classReference;

        return $classReference;
    }

    /**
     * @param string[] $templateTypes
     */
    public function newFunction(string $functionName, array $templateTypes = []): FunctionReferenceBuilder
    {
        $functionReference = FunctionReferenceBuilder::create($this->filepath, $functionName, $templateTypes);
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
