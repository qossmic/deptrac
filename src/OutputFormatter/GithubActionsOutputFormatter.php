<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Console\Output;
use SensioLabs\Deptrac\Env;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;

class GithubActionsOutputFormatter implements OutputFormatterInterface
{
    private const REPORT_UNCOVERED = 'report-uncovered';

    /** @var Env */
    private $env;

    public function __construct(Env $env = null)
    {
        $this->env = $env ?? new Env();
    }

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
        return [
            OutputFormatterOption::newValueOption(static::REPORT_UNCOVERED, 'report uncovered dependencies', false),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false !== $this->env->get('GITHUB_ACTIONS');
    }

    /**
     * {@inheritdoc}
     */
    public function finish(Context $context, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        foreach ($context->all() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }

            $dependency = $rule->getDependency();
            $output->writeLineFormatted(sprintf(
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

        if (true === $outputFormatterInput->getOptionAsBoolean(static::REPORT_UNCOVERED)) {
            $this->printUncovered($context, $output);
        }
    }

    private function determineLogLevel(Rule $rule): string
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

    private function printUncovered(Context $context, Output $output): void
    {
        $uncovered = $context->uncovered();
        if ([] === $uncovered) {
            return;
        }

        foreach ($uncovered as $u) {
            $dependency = $u->getDependency();
            $output->writeLineFormatted(
                sprintf(
                    '::warning file=%s,line=%s::%s has uncovered dependency on %s (%s)',
                    $dependency->getFileOccurrence()->getFilepath(),
                    $dependency->getFileOccurrence()->getLine(),
                    $dependency->getClassLikeNameA()->toString(),
                    $dependency->getClassLikeNameB()->toString(),
                    $u->getLayer()
                )
            );
        }
    }
}
