<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner;

use ArrayObject;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SplStack;

class AstMap
{
    /**
     * @var AstClassReference[]
     */
    private $astClassReferences = [];

    /**
     * @var AstFileReference[]
     */
    private $astFileReferences = [];

    /**
     * @param AstFileReference[] $astFileReferences
     */
    public function __construct(array $astFileReferences)
    {
        foreach ($astFileReferences as $astFileReference) {
            $this->addAstFileReference($astFileReference);
        }
    }

    /**
     * @return AstClassReference[]
     */
    public function getAstClassReferences(): array
    {
        return $this->astClassReferences;
    }

    /**
     * @return AstFileReference[]
     */
    public function getAstFileReferences(): array
    {
        return $this->astFileReferences;
    }

    public function getClassReferenceByClassName(ClassLikeName $className): ?AstClassReference
    {
        return $this->astClassReferences[$className->toString()] ?? null;
    }

    /**
     * @return iterable<AstInherit>
     */
    public function getClassInherits(ClassLikeName $classLikeName): iterable
    {
        $classReference = $this->getClassReferenceByClassName($classLikeName);

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $dep) {
            yield $dep;
            yield from $this->resolveDepsRecursive($dep);
        }
    }

    /**
     * @param ArrayObject<string, true>|null $alreadyResolved
     *
     * @return iterable<AstInherit>
     */
    private function resolveDepsRecursive(
        AstInherit $inheritDependency,
        ArrayObject $alreadyResolved = null,
        SplStack $pathStack = null
    ): iterable {
        /** @var ArrayObject<string, true> $alreadyResolved */
        $alreadyResolved = $alreadyResolved ?? new ArrayObject();

        if (null === $pathStack) {
            $pathStack = new SplStack();
            $pathStack->push($inheritDependency);
        }

        $className = $inheritDependency->getClassLikeName()->toString();

        if (isset($alreadyResolved[$className])) {
            $pathStack->pop();

            return [];
        }

        $classReference = $this->getClassReferenceByClassName($inheritDependency->getClassLikeName());

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $inherit) {
            $alreadyResolved[$className] = true;

            /** @var AstInherit[] $path */
            $path = iterator_to_array($pathStack);
            yield $inherit->withPath($path);

            $pathStack->push($inherit);

            yield from $this->resolveDepsRecursive($inherit, $alreadyResolved, $pathStack);

            unset($alreadyResolved[$className]);
            $pathStack->pop();
        }
    }

    private function addAstClassReference(AstClassReference $astClassReference): void
    {
        $this->astClassReferences[$astClassReference->getClassLikeName()->toString()] = $astClassReference;
    }

    private function addAstFileReference(AstFileReference $astFileReference): void
    {
        $this->astFileReferences[$astFileReference->getFilepath()] = $astFileReference;

        foreach ($astFileReference->getAstClassReferences() as $astClassReference) {
            $this->addAstClassReference($astClassReference);
        }
    }
}
