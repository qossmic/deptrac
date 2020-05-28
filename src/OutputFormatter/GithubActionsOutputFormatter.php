<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

class GithubActionsOutputFormatter implements OutputFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'github-actions';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(): array
    {
        return [];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function finish(Context $context, OutputInterface $output, OutputFormatterInput $outputFormatterInput): void
    {
        foreach ($context->all() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }

            $dependency = $rule->getDependency();
            $output->writeln(sprintf(
                '::%s file=%s,line=%s::%s%s must not depend on %s (%s on %s)',
                $this->determineLogLevel($rule),
                $dependency->getFileOccurrence()->getFilepath(),
                $dependency->getFileOccurrence()->getLine(),
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getClassLikeNameA()->toString(),
                $dependency->getClassLikeNameB()->toString(),
                $rule->getLayerA(),
                $rule->getLayerB()
            ));
        }
    }

    public function determineLogLevel(Rule $rule): string
    {
        switch (get_class($rule)) {
            case Violation::class:
                return 'error';
            case SkippedViolation::class:
                return 'warning';
            default:
                return 'debug';
        }
    }
}
