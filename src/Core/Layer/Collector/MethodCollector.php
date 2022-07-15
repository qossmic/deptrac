<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;

final class MethodCollector extends RegexCollector
{
    private NikicPhpParser $astParser;

    public function __construct(NikicPhpParser $astParser)
    {
        $this->astParser = $astParser;
    }

    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $pattern = $this->getValidatedPattern($config);

        $classLike = $this->astParser->getNodeForClassLikeReference($reference);

        if (null === $classLike) {
            return false;
        }

        foreach ($classLike->getMethods() as $classMethod) {
            if (1 === preg_match($pattern, (string) $classMethod->name)) {
                return true;
            }
        }

        return false;
    }

    protected function getPattern(array $config): string
    {
        if (isset($config['name']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'MethodCollector should use the "value" key from this version');
            $config['value'] = $config['name'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('MethodCollector needs the name configuration.');
        }

        return '/'.$config['value'].'/i';
    }
}
