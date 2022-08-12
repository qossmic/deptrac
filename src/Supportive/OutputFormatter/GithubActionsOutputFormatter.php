<?php

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\RuleInterface;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;

final class GithubActionsOutputFormatter implements OutputFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'github-actions';
    }

    /**
     * {@inheritdoc}
     */
    public function finish(LegacyResult $result, OutputInterface $output, OutputFormatterInput $outputFormatterInput): void
    {
        foreach ($result->rules as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }
            if ($rule instanceof SkippedViolation && !$outputFormatterInput->reportSkipped) {
                continue;
            }

            $dependency = $rule->getDependency();

            $message = sprintf(
                '%s%s must not depend on %s (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getDepender()->toString(),
                $dependency->getDependent()->toString(),
                $rule->getDependerLayer(),
                $rule->getDependentLayer()
            );

            if ($dependency instanceof InheritDependency) {
                $message .= '%0A'.$this->inheritPathMessage($dependency);
            }

            $output->writeLineFormatted(sprintf(
                '::%s file=%s,line=%s::%s',
                $this->determineLogLevel($rule),
                $dependency->getFileOccurrence()->filepath,
                $dependency->getFileOccurrence()->line,
                $message
            ));
        }

        if ($outputFormatterInput->reportUncovered && $result->hasUncovered()) {
            $this->printUncovered($result, $output, $outputFormatterInput->failOnUncovered);
        }

        if ($result->hasErrors()) {
            $this->printErrors($result, $output);
        }

        if ($result->hasWarnings()) {
            $this->printWarnings($result, $output);
        }
    }

    private function determineLogLevel(RuleInterface $rule): string
    {
        return match ($rule::class) {
            Violation::class => 'error',
            SkippedViolation::class => 'warning',
            default => 'debug',
        };
    }

    private function printUncovered(LegacyResult $result, OutputInterface $output, bool $reportAsError): void
    {
        foreach ($result->uncovered() as $u) {
            $dependency = $u->getDependency();
            $output->writeLineFormatted(
                sprintf(
                    '::%s file=%s,line=%s::%s has uncovered dependency on %s (%s)',
                    $reportAsError ? 'error' : 'warning',
                    $dependency->getFileOccurrence()->filepath,
                    $dependency->getFileOccurrence()->line,
                    $dependency->getDepender()->toString(),
                    $dependency->getDependent()->toString(),
                    $u->layer
                )
            );
        }
    }

    private function inheritPathMessage(InheritDependency $dependency): string
    {
        $buffer = [];
        $astInherit = $dependency->inheritPath;
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf('%s::%d', $p->classLikeName->toString(), $p->fileOccurrence->line));
        }

        $buffer[] = sprintf('%s::%d', $astInherit->classLikeName->toString(), $astInherit->fileOccurrence->line);
        $buffer[] = sprintf(
            '%s::%d',
            $dependency->originalDependency->getDependent()->toString(),
            $dependency->originalDependency->getFileOccurrence()->line
        );

        return implode(' ->%0A', $buffer);
    }

    private function printErrors(LegacyResult $result, OutputInterface $output): void
    {
        foreach ($result->errors as $error) {
            $output->writeLineFormatted('::error ::'.(string) $error);
        }
    }

    private function printWarnings(LegacyResult $result, OutputInterface $output): void
    {
        foreach ($result->warnings as $warning) {
            $output->writeLineFormatted('::warning ::'.(string) $warning);
        }
    }
}
