<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;

use function str_contains;

class AttributeCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        if (!$reference instanceof FileReference
            && !$reference instanceof ClassLikeReference
            && !$reference instanceof FunctionLikeReference
        ) {
            return false;
        }

        $match = $this->getSearchedSubstring($config);

        foreach ($reference->dependencies as $dependency) {
            if (DependencyType::ATTRIBUTE !== $dependency->type) {
                continue;
            }

            $usedAttribute = $dependency->token->toString();

            if (str_contains($usedAttribute, $match)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @throws InvalidLayerDefinitionException
     */
    private function getSearchedSubstring(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw InvalidLayerDefinitionException::invalidCollectorConfiguration('AttributeCollector needs the attribute name as a string.');
        }

        return $config['value'];
    }
}
