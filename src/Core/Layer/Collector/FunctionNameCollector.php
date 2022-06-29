<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

final class FunctionNameCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof FunctionLikeReference) {
            return false;
        }

        /** @var FunctionLikeToken $tokenName */
        $tokenName = $reference->getToken();

        return $tokenName->match($this->getPattern($config));
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     */
    private function getPattern(array $config): string
    {
        if (isset($config['regex']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'FunctionNameCollector should use the "value" key from this version');
            $config['value'] = $config['regex'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('FunctionNameCollector needs the regex configuration.');
        }

        return '/'.$config['value'].'/i';
    }
}
