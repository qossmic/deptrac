<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

class PhpLoader implements LoaderInterface
{
    /**
     * @psalm-suppress MixedInferredReturnType
     */
    public function load(string $file): Configuration
    {
        /**
         * @psalm-suppress MixedReturnStatement
         * @psalm-suppress UnresolvableInclude This `require` is a runtime dependency
         */
        return require $file;
    }
}
