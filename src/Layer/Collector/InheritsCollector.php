<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

final class InheritsCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $classLikeName = $this->getClassLikeName($config);

        foreach ($astMap->getClassInherits($reference->getToken()) as $inherit) {
            if ($inherit->getClassLikeName()->equals($classLikeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    private function getClassLikeName(array $config): ClassLikeToken
    {
        if (isset($config['inherits']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'InheritsCollector should use the "value" key from this version');
            $config['value'] = $config['inherits'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('InheritsCollector needs the interface, trait or class name as a string.');
        }

        return ClassLikeToken::fromFQCN($config['value']);
    }
}
