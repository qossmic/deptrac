<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\InputCollector\InputCollectorInterface;

/**
 * @internal
 */
class AstMapExtractor
{
    private InputCollectorInterface $inputCollector;
    private AstLoader $astLoader;

    private ?AstMap $astMapCache = null;

    public function __construct(InputCollectorInterface $inputCollector, AstLoader $astLoader)
    {
        $this->inputCollector = $inputCollector;
        $this->astLoader = $astLoader;
    }

    public function extract(): AstMap
    {
        if (null === $this->astMapCache) {
            $this->astMapCache = $this->astLoader->createAstMap($this->inputCollector->collect());
        }

        return $this->astMapCache;
    }
}
