<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;

final class ComposerCollector implements CollectorInterface
{
    public function __construct()
    {
    }

    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        if (!isset($config['composerPath']) || !is_string($config['composerPath'])) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('ComposerCollector needs the path to the composer.json file as string.');
        }

        if (!isset($config['composerLockPath']) || !is_string($config['composerLockPath'])) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('ComposerCollector needs the path to the composer.lock file as string.');
        }

        if (!isset($config['packages']) || !is_array($config['packages'])) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('ComposerCollector needs the list of packages as string.');
        }

        $composerFilesParser = new ComposerFilesParser($config['composerPath'], $config['composerLockPath']);
        $namespaces = $composerFilesParser->autoloadableNamespacesForRequirements($config['packages'], true);
        $token = $reference->getToken()->toString();

        foreach ($namespaces as $namespace) {
            if (str_starts_with($token, $namespace)) {
                return true;
            }
        }

        return false;
    }

}
