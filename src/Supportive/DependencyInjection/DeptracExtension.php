<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\DependencyInjection;

use Qossmic\Deptrac\Contract\Config\EmitterType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

use function getcwd;

/**
 * @psalm-suppress UndefinedDocblockClass
 * @psalm-suppress MixedArgument
 */
class DeptracExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter('paths', $configs['paths']);
        $container->setParameter('exclude_files', $configs['exclude_files']);
        $container->setParameter('layers', $configs['layers']);
        $container->setParameter('ruleset', $configs['ruleset']);
        $container->setParameter('skip_violations', $configs['skip_violations']);
        $container->setParameter('formatters', $configs['formatters'] ?? []);
        $container->setParameter('analyser', $configs['analyser']);
        $container->setParameter('ignore_uncovered_internal_classes', $configs['ignore_uncovered_internal_classes']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('projectDirectory')) {
            $projectDirectory = getcwd();
            if ($container->hasParameter('depfileDirectory')) {
                /** @throws void */
                $projectDirectory = $container->getParameter('depfileDirectory');
                /** @throws void */
            } elseif ($container->hasParameter('workingDirectory')) {
                /** @throws void */
                $projectDirectory = $container->getParameter('workingDirectory');
            }
            $container->setParameter('projectDirectory', $projectDirectory);
        }
        if (!$container->hasParameter('paths')) {
            $container->setParameter('paths', []);
        }
        if (!$container->hasParameter('exclude_files')) {
            $container->setParameter('exclude_files', []);
        }
        if (!$container->hasParameter('layers')) {
            $container->setParameter('layers', []);
        }
        if (!$container->hasParameter('ruleset')) {
            $container->setParameter('ruleset', []);
        }
        if (!$container->hasParameter('skip_violations')) {
            $container->setParameter('skip_violations', []);
        }
        if (!$container->hasParameter('formatters')) {
            $container->setParameter('formatters', []);
        }
        if (!$container->hasParameter('analyser')) {
            $container->setParameter('analyser', ['types' => [EmitterType::CLASS_TOKEN->value, EmitterType::USE_TOKEN->value]]);
        }
        if (!$container->hasParameter('ignore_uncovered_internal_classes')) {
            $container->setParameter('ignore_uncovered_internal_classes', true);
        }
    }
}
