<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\Configuration\Configuration;

class UnassignedAnalyser
{
    private AstRunner $astRunner;
    private FileResolver $fileResolver;
    private TokenLayerResolverFactory $tokenLayerResolverFactory;

    public function __construct(
        AstRunner $astRunner,
        FileResolver $fileResolver,
        TokenLayerResolverFactory $tokenLayerResolverFactory
    ) {
        $this->astRunner = $astRunner;
        $this->fileResolver = $fileResolver;
        $this->tokenLayerResolverFactory = $tokenLayerResolverFactory;
    }

    /**
     * @return string[]
     */
    public function analyse(Configuration $configuration): array
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration));
        $tokenLayerResolver = $this->tokenLayerResolverFactory->create($configuration, $astMap);

        /** @var string[] $tokenNames */
        $tokenNames = [];

        foreach ($astMap->getAstFileReferences() as $fileReference) {
            foreach ($fileReference->getAstClassReferences() as $classReference) {
                $tokenName = $classReference->getTokenName();
                if ([] === $tokenLayerResolver->getLayersByTokenName($tokenName)) {
                    $tokenNames[] = $tokenName->toString();
                }
            }

            foreach ($fileReference->getFunctionReferences() as $functionReference) {
                $tokenName = $functionReference->getTokenName();
                if ([] === $tokenLayerResolver->getLayersByTokenName($tokenName)) {
                    $tokenNames[] = $tokenName->toString();
                }
            }

            $tokenName = $fileReference->getTokenName();
            if ([] === $tokenLayerResolver->getLayersByTokenName($tokenName)) {
                $tokenNames[] = $tokenName->toString();
            }
        }

        natcasesort($tokenNames);

        return array_values($tokenNames);
    }
}
