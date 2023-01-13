<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;

final class ClassNameRegexCollector extends RegexCollector
{
    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        return $reference->getToken()->match($this->getValidatedPattern($config));
    }

    protected function getPattern(array $config): string
    {
        if (isset($config['regex']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'ClassNameRegexCollector should use the "value" key from this version');
            $config['value'] = $config['regex'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw InvalidLayerDefinitionException::invalidCollectorConfiguration('ClassNameRegexCollector needs the regex configuration.');
        }

        return $config['value'];
    }
}
