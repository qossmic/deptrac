<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use function str_contains;

class AttributeCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof FileReference
            && !$reference instanceof ClassLikeReference
            && !$reference instanceof FunctionLikeReference
        ) {
            return false;
        }

        $match = $this->getSearchedSubstring($config);

        foreach ($reference->getDependencies() as $dependency) {
            if (DependencyToken::ATTRIBUTE !== $dependency->getType()) {
                continue;
            }

            $usedAttribute = $dependency->getToken()->toString();

            if (str_contains($usedAttribute, $match)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    private function getSearchedSubstring(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('AttributeCollector needs the attribute name as a string.');
        }

        return $config['value'];
    }
}
