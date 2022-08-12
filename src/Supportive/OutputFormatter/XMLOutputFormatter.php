<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use DOMAttr;
use DOMDocument;
use DOMElement;
use Exception;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;

/**
 * @internal
 */
final class XMLOutputFormatter implements OutputFormatterInterface
{
    private const DEFAULT_PATH = './deptrac-report.xml';

    public static function getName(): string
    {
        return 'xml';
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function finish(
        LegacyResult $result,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $xml = $this->createXml($result);

        $dumpXmlPath = $outputFormatterInput->outputPath ?? self::DEFAULT_PATH;
        file_put_contents($dumpXmlPath, $xml);
        $output->writeLineFormatted('<info>XML Report dumped to '.realpath($dumpXmlPath).'</info>');
    }

    /**
     * @throws Exception
     */
    private function createXml(LegacyResult $dependencyContext): string
    {
        if (!class_exists(DOMDocument::class)) {
            throw new Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $rootEntry = $xmlDoc->createElement('entries');

        foreach ($dependencyContext->violations() as $rule) {
            $this->addRule('violation', $rootEntry, $xmlDoc, $rule);
        }

        foreach ($dependencyContext->skippedViolations() as $rule) {
            $this->addRule('skipped_violation', $rootEntry, $xmlDoc, $rule);
        }

        $xmlDoc->appendChild($rootEntry);

        return (string) $xmlDoc->saveXML();
    }

    private function addRule(string $type, DOMElement $rootEntry, DOMDocument $xmlDoc, Violation|SkippedViolation $rule): void
    {
        $entry = $xmlDoc->createElement('entry');
        $entry->appendChild(new DOMAttr('type', $type));

        $entry->appendChild($xmlDoc->createElement('LayerA', $rule->getDependerLayer()));
        $entry->appendChild($xmlDoc->createElement('LayerB', $rule->getDependentLayer()));

        $dependency = $rule->getDependency();
        $entry->appendChild($xmlDoc->createElement('ClassA', $dependency->getDepender()->toString()));
        $entry->appendChild($xmlDoc->createElement('ClassB', $dependency->getDependent()->toString()));

        $fileOccurrence = $dependency->getFileOccurrence();
        $occurrence = $xmlDoc->createElement('occurrence');
        $occurrence->setAttribute('file', $fileOccurrence->filepath);
        $occurrence->setAttribute('line', (string) $fileOccurrence->line);
        $entry->appendChild($occurrence);

        $rootEntry->appendChild($entry);
    }
}
