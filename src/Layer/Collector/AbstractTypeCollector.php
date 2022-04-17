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

        if (
            false === (
                ClassLikeType::classLike()->matches($this->getType()) ||
                $reference->getType()->matches($this->getType())
            )) {
            return false;
        }

        return $reference->getToken()->match($this->getValidatedPattern($config));
    }

    protected function getPattern(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException(sprintf('%s needs the value configuration.', self::class));
        }

        return '/'.$config['value'].'/i';
    }
}
