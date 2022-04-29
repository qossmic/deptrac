<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

final class UsesCollector implements CollectorInterface
{
    public function resolvable(array $config): bool
    {
        return true;
    }

    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $interfaceName = $this->getInterfaceName($config);

        foreach ($astMap->getClassInherits($reference->getToken()) as $inherit) {
            if ($inherit->isUses() && $inherit->getClassLikeName()->equals($interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, string|array<string, string>> $config
     */
    private function getInterfaceName(array $config): ClassLikeToken
    {
        if (isset($config['uses']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'UsesCollector should use the "value" key from this version');
            $config['value'] = $config['uses'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('UsesCollector needs the trait name as a string.');
        }

        return ClassLikeToken::fromFQCN($config['value']);
    }
}
