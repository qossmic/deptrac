<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\AstRunner\AstMap\TokenName;
use Qossmic\Deptrac\AstRunner\AstRunner;
use Qossmic\Deptrac\Configuration\Configuration;

class TokenAnalyser
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
    public function analyse(Configuration $configuration, TokenName $tokenName): array
    {
        $astMap = $this->astRunner->createAstMapByFiles($this->fileResolver->resolve($configuration));

        return $this->tokenLayerResolverFactory
            ->create($configuration, $astMap)
            ->getLayersByTokenName($tokenName);
    }
}
