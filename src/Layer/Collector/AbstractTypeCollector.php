<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use function is_string;

abstract class AbstractTypeCollector extends RegexCollector
{
    abstract protected function getType(): ClassLikeType;

    public function resolvable(array $config): bool
    {
        return true;
    }

    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $isClassLike = ClassLikeType::classLike()->matches($this->getType());
        $isSameType = $reference->getType()->matches($this->getType());

        return ($isClassLike || $isSameType) && $reference->getToken()->match($this->getValidatedPattern($config));
    }

    protected function getPattern(array $config): string
    {
        if (isset($config['regex']) && !isset($config['value'])) {
            trigger_deprecation(
                'qossmic/deptrac',
                '0.20.0',
                sprintf('Collector "%s" should use the "value" key from this version', $this->getType()->toString())
            );
            $config['value'] = $config['regex'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException(sprintf('Collector "%s" needs the regex configuration.', $this->getType()->toString()));
        }

        return '/'.$config['value'].'/i';
    }
}
