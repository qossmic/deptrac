<?php

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class GithubActionsOutputFormatter implements OutputFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'github-actions';
    }

    public static function getConfigName(): string
    {
        return self::getName();
    }

    /**
     * {@inheritdoc}
     */
    public function finish(Context $context, Output $output, OutputFormatterInput $outputFormatterInput): void
    {
        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }
            if ($rule instanceof SkippedViolation && !$outputFormatterInput->getReportSkipped()) {
                continue;
            }

            $dependency = $rule->getDependency();

            $message = sprintf(
                '%s%s must not depend on %s (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getDependant()->toString(),
                $dependency->getDependee()->toString(),
                $rule->getDependantLayerName(),
                $rule->getDependeeLayerName()
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

        if ($outputFormatterInput->getReportUncovered() && $context->hasUncovered()) {
            $this->printUncovered($context, $output, $outputFormatterInput->getFailOnUncovered());
        }

        if ($context->hasErrors()) {
            $this->printErrors($context, $output);
        }

        if ($context->hasWarnings()) {
            $this->printWarnings($context, $output);
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

    private function printUncovered(Context $context, Output $output, bool $reportAsError): void
    {
        foreach ($context->uncovered() as $u) {
            $dependency = $u->getDependency();
            $output->writeLineFormatted(
                sprintf(
                    '::%s file=%s,line=%s::%s has uncovered dependency on %s (%s)',
                    $reportAsError ? 'error' : 'warning',
                    $dependency->getFileOccurrence()->getFilepath(),
                    $dependency->getFileOccurrence()->getLine(),
                    $dependency->getDependant()->toString(),
                    $dependency->getDependee()->toString(),
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
            $dependency->getOriginalDependency()->getDependee()->toString(),
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

    private function printWarnings(Context $context, Output $output): void
    {
        foreach ($context->warnings() as $error) {
            $output->writeLineFormatted('::warning ::'.$error->toString());
        }
    }
}
