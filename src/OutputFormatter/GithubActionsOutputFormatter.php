<?php

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\Env;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class GithubActionsOutputFormatter implements OutputFormatterInterface
{
    /** @deprecated */
    public const LEGACY_REPORT_UNCOVERED = 'formatter-github-actions-report-uncovered';

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
            OutputFormatterOption::newValueOption(self::LEGACY_REPORT_UNCOVERED, '<fg=yellow>[DEPRECATED]</> report uncovered dependencies', false),
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
        $legacyReportUncovered = $outputFormatterInput->getOptionAsBoolean(self::LEGACY_REPORT_UNCOVERED);

        if ($legacyReportUncovered) {
            $output->writeLineFormatted(sprintf('⚠️  You\'re using an obsolete option <fg=cyan>--%s</>. ⚠️️', self::LEGACY_REPORT_UNCOVERED));
            $output->writeLineFormatted(sprintf('   Please use the new option <fg=cyan>--%s</> instead.', AnalyzeCommand::OPTION_REPORT_UNCOVERED));
            $output->writeLineFormatted('');
        }

        $reportSkipped = $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_SKIPPED);

        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }
            if (!$reportSkipped && $rule instanceof SkippedViolation) {
                continue;
            }

            $dependency = $rule->getDependency();

            $message = sprintf(
                '%s%s must not depend on %s (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getClassLikeNameA()->toString(),
                $dependency->getClassLikeNameB()->toString(),
                $rule->getLayerA(),
                $rule->getLayerB()
            );

            if ($dependency instanceof InheritDependency) {
                $message .= '%0A'.$this->inheritPathMessage($dependency);
            }

            $output->writeLineFormatted(sprintf(
                '::%s file=%s,line=%s::%s',
                $this->determineLogLevel($rule),
                $dependency->getFileOccurrence()->getFilepath(),
                $dependency->getFileOccurrence()->getLine(),
                $message
            ));
        }

        if ($legacyReportUncovered
            || $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_UNCOVERED)) {
            $this->printUncovered($context, $output);
        }

        if ($context->hasErrors()) {
            $this->printErrors($context, $output);
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

    private function inheritPathMessage(InheritDependency $dependency): string
    {
        $buffer = [];
        $astInherit = $dependency->getInheritPath();
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf('%s::%d', $p->getClassLikeName()->toString(), $p->getFileOccurrence()->getLine()));
        }

        $buffer[] = sprintf('%s::%d', $astInherit->getClassLikeName()->toString(), $astInherit->getFileOccurrence()->getLine());
        $buffer[] = sprintf(
            '%s::%d',
            $dependency->getOriginalDependency()->getClassLikeNameB()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        return implode(' ->%0A', $buffer);
    }

    private function printErrors(Context $context, Output $output): void
    {
        foreach ($context->errors() as $error) {
            $output->writeLineFormatted('::error ::'.$error->toString());
        }
    }
}
