<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser;

use Qossmic\Deptrac\Ast\AstLoader;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\InputCollector\InputCollectorInterface;

class AstMapExtractor
{
    private InputCollectorInterface $inputCollector;
    private AstLoader $astLoader;

    public function __construct(InputCollectorInterface $inputCollector, AstLoader $astLoader)
    {
        $this->inputCollector = $inputCollector;
        $this->astLoader = $astLoader;
    }

    public function extract(): AstMap
    {
        return $this->astLoader->createAstMap($this->inputCollector->collect());
    }
}
