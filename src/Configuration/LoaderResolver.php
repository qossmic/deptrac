<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

class LoaderResolver
{
    private Loader $ymlLoader;

    public function __construct(Loader $ymlLoader)
    {
        $this->ymlLoader = $ymlLoader;
    }

    public function resolve(string $filename): LoaderInterface
    {
        if (false !== strpos($filename, '.php')) {
            return new PhpLoader();
        }

        return $this->ymlLoader;
    }
}
