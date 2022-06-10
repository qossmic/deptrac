<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

final class ImplementsCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $interfaceName = $this->getInterfaceName($config);

        foreach ($astMap->getClassInherits($reference->getToken()) as $inherit) {
            if ($inherit->isImplements() && $inherit->getClassLikeName()->equals($interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    private function getInterfaceName(array $config): ClassLikeToken
    {
        if (isset($config['implements']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'ImplementsCollector should use the "value" key from this version');
            $config['value'] = $config['implements'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('ImplementsCollector needs the interface name as a string.');
        }

        return ClassLikeToken::fromFQCN($config['value']);
    }
}
